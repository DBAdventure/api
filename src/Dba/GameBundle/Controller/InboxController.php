<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Inbox;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Form;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/inbox")
 */
class InboxController extends BaseController
{
    /**
     * @Route("", name="inbox", methods="GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $messages = $this->repos()->getInboxRepository()->findBy(
            [
                'recipient' => $this->getUser(),
                'recipientDirectory' => Inbox::DIRECTORY_INBOX
            ],
            [
                'createdAt' => 'DESC'
            ]
        );

        return $this->displayList($request, $messages, Inbox::DIRECTORY_INBOX);
    }

    /**
     * @Route("/write/{messageId}", name="inbox.write", methods={"GET", "POST"},
              defaults={"messageId": null}, requirements={"messageId": "\d+"})
     * @ParamConverter("message", class="Dba\GameBundle\Entity\Inbox", isOptional="true", options={"id" = "messageId"})
     * @Route("/write/player/{playerId}", name="inbox.write.player", methods={"GET", "POST"},
              defaults={"playerId": null}, requirements={"playerId": "\d+"})
     * @ParamConverter("player", class="Dba\GameBundle\Entity\Player", options={"id" = "playerId"})
     * @Route("/write/guild", name="inbox.write.guild", methods={"GET", "POST"}, defaults={"guild": true})
     * @Template()
     */
    public function writeAction(Request $request, Inbox $message = null, Player $player = null, $guild = null)
    {
        $originalInbox = new Inbox();
        if (!empty($message) && in_array(
            $this->getUser()->getId(),
            [$message->getRecipient()->getId(), $message->getSender()->getId()]
        )
        ) {
            $message->setMessage(
                PHP_EOL . PHP_EOL .
                preg_replace(
                    '~^(.*)$~Um',
                    '| $1',
                    $message->getMessage()
                )
            );
            $message->setSubject(
                preg_replace(
                    '~^(?:(?!RE: )(.*))$~Ui',
                    'RE: $1',
                    $message->getSubject()
                )
            );
            $originalInbox->setSubject($message->getSubject());
            $originalInbox->setMessage($message->getMessage());
        } else {
            $message = null;
        }


        if ($originalInbox->getRecipients()->count() === 0) {
            if (!empty($player) && $player->isPlayer()) {
                $originalInbox->addRecipient($player);
            } else {
                if (empty($guild) || empty($this->getUser()->getGuildPlayer())) {
                    $originalInbox->addRecipient(new Player());
                } else {
                    $guildPlayers = $this->getUser()->getGuildPlayer()->getGuild()->getPlayers();
                    foreach ($guildPlayers as $guildPlayer) {
                        if ($guildPlayer->isEnabled() &&
                            $guildPlayer->getPlayer()->getId() != $this->getUser()->getId()
                        ) {
                            $originalInbox->addRecipient($guildPlayer->getPlayer());
                        }
                    }
                }
            }
        }

        $form = $this->createForm(Form\InboxMessage::class, $originalInbox);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $playerRepo = $this->repos()->getPlayerRepository();
            if (!empty($message)) {
                $originalInbox->addRecipient($message->getSender());
            }

            $originalInbox->setSender($this->getUser());
            $recipients = [];
            foreach ($originalInbox->getRecipients() as $recipient) {
                $recipient = $playerRepo->findOneByName($recipient->getName());
                if (empty($recipient) ||
                    $recipient->getId() == $this->getUser()->getId() ||
                    !$recipient->isPlayer()
                ) {
                    continue;
                }

                $inbox = clone $originalInbox;
                $inbox->setRecipient($recipient);
                $this->em()->detach($inbox);
                $this->em()->persist($inbox);
                $recipients[] = $recipient->getName();
            }


            if (!empty($recipients)) {
                $this->em()->flush();
                $this->addFlash(
                    'success',
                    $this->trans('inbox.message.sent', ['%names%' => implode($recipients, ', ')])
                );
            }

            return $this->redirect($this->generateUrl('inbox'));
        }

        if ($request->isXmlHttpRequest()) {
            return $this->jsonContent(
                'DbaGameBundle::inbox/write.html.twig',
                [
                    'form' => $form->createView(),
                    'message' => $message
                ]
            );
        }

        return $this->render(
            'DbaGameBundle::inbox/index.html.twig',
            [
                'page' => 'write',
                'form' => $form->createView(),
                'message' => $message
            ]
        );
    }

    /**
     * @Route("/read/{id}", name="inbox.read", methods={"GET"}, defaults={"id": null})
     * @ParamConverter("message", class="Dba\GameBundle\Entity\Inbox")
     * @Template()
     */
    public function readAction(Request $request, Inbox $message)
    {
        $playerId = $this->getUser()->getId();
        $recipientId = $message->getRecipient()->getId();
        $senderId = $message->getSender()->getId();
        if (!in_array($playerId, [$senderId, $recipientId])
        ) {
            return $this->forbidden();
        }

        if ($recipientId != $playerId) {
            $directory = $message->getSenderDirectory();
        } else {
            $directory = $message->getRecipientDirectory();
            if ($directory == Inbox::DIRECTORY_INBOX
                && $message->getStatus() == Inbox::STATUS_UNREAD
            ) {
                $message->setStatus(Inbox::STATUS_READ);
                $this->em()->persist($message);
                $this->em()->flush();
            }
        }

        if ($directory == Inbox::DIRECTORY_TRASH) {
            return $this->forbidden();
        }

        if ($request->isXmlHttpRequest()) {
            return $this->jsonContent(
                'DbaGameBundle::inbox/read.html.twig',
                [
                    'message' => $message,
                    'directory' => $directory
                ]
            );
        }

        return $this->render(
            'DbaGameBundle::inbox/index.html.twig',
            [
                'page' => 'read',
                'message' => $message,
                'directory' => $directory
            ]
        );
    }

    /**
     * @Route("/outbox", name="inbox.outbox", methods={"GET"})
     * @Template()
     */
    public function outboxAction(Request $request)
    {
        $messages = $this->repos()->getInboxRepository()->findBy(
            [
                'sender' => $this->getUser(),
                'senderDirectory' => Inbox::DIRECTORY_OUTBOX
            ],
            [
                'createdAt' => 'DESC'
            ]
        );
        return $this->displayList($request, $messages, Inbox::DIRECTORY_OUTBOX);
    }

    protected function displayList(Request $request, array $messages, $directory)
    {
        $parameters = [
            'messages' => $messages,
            'directory' => $directory,
            'page' => 'list'
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->jsonContent(
                'DbaGameBundle::inbox/list.html.twig',
                $parameters
            );
        }

        return $this->render(
            'DbaGameBundle::inbox/index.html.twig',
            $parameters
        );
    }

    /**
     * @Route("/archive/{id}", name="inbox.archive", methods={"GET"}, defaults={"id": null})
     * @ParamConverter("message", class="Dba\GameBundle\Entity\Inbox", isOptional="true", options={"id" = "id"})
     * @Template()
     */
    public function archiveAction(Request $request, Inbox $message = null)
    {
        if (!empty($message) && in_array(
            $this->getUser()->getId(),
            [$message->getRecipient()->getId(), $message->getSender()->getId()]
        ) &&
            $message->canArchive()
        ) {
            if ($this->getUser()->getId() == $message->getRecipient()->getId()) {
                $message->setRecipientDirectory(Inbox::DIRECTORY_ARCHIVE);
            } else {
                $message->setSenderDirectory(Inbox::DIRECTORY_ARCHIVE);
            }

            $this->em()->persist($message);
            $this->em()->flush();
        }

        $messages = $this->repos()->getInboxRepository()
                  ->findArchive(
                      $this->getUser()
                  );
        return $this->displayList($request, $messages, Inbox::DIRECTORY_ARCHIVE);
    }

    /**
     * @Route("/clear/{what}", name="inbox.clear", methods={"GET"})
     * @Template()
     */
    public function clearAction($what)
    {
        $player = $this->getUser();
        $inboxRepo = $this->repos()->getInboxRepository();
        $route = 'inbox';
        switch ($what) {
            case Inbox::DIRECTORY_INBOX:
                $messages = $inboxRepo->findBy(
                    [
                        'recipient' => $player,
                        'recipientDirectory' => Inbox::DIRECTORY_INBOX
                    ]
                );
                break;
            case Inbox::DIRECTORY_OUTBOX:
                $route = 'inbox.' . $what;
                $messages = $inboxRepo->findBy(
                    [
                        'sender' => $player,
                        'senderDirectory' => Inbox::DIRECTORY_OUTBOX
                    ]
                );
                break;
            case Inbox::DIRECTORY_ARCHIVE:
                $route = 'inbox.' . $what;
                $messages = $inboxRepo->findArchive(
                    $player
                );
                break;
            case 'read':
                $messages = $inboxRepo->findBy(
                    [
                        'recipient' => $player,
                        'recipientDirectory' => Inbox::DIRECTORY_INBOX,
                        'status' => Inbox::STATUS_READ
                    ]
                );
                break;
        }

        foreach ($messages as $message) {
            if ($message->getRecipient()->getId() == $player->getId()) {
                $message->setRecipientDirectory(Inbox::DIRECTORY_TRASH);
            } else {
                $message->setSenderDirectory(Inbox::DIRECTORY_TRASH);
            }

            $this->em()->persist($message);
        }

        $this->em()->flush();

        return $this->redirect($this->generateUrl($route));
    }

    /**
     * @Route("/delete/{id}", name="inbox.delete", methods={"GET"})
     * @ParamConverter("message", class="Dba\GameBundle\Entity\Inbox")
     * @Template()
     */
    public function deleteAction(Inbox $message)
    {
        $playerId = $this->getUser()->getId();
        $recipientId = $message->getRecipient()->getId();
        $senderId = $message->getSender()->getId();
        if (!in_array($playerId, [$senderId, $recipientId])
        ) {
            return $this->forbidden();
        }

        if ($recipientId != $playerId) {
            $message->setSenderDirectory(Inbox::DIRECTORY_TRASH);
        } else {
            $message->setRecipientDirectory(Inbox::DIRECTORY_TRASH);
        }

        $this->em()->persist($message);
        $this->em()->flush();

        return $this->redirect($this->generateUrl('inbox'));
    }
}
