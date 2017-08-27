<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Inbox;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Form;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class InboxController extends BaseController
{
    public function getInboxAction()
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

        return $this->displayList($messages, Inbox::DIRECTORY_INBOX);
    }

    public function getOutboxAction()
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
        return $this->displayList($messages, Inbox::DIRECTORY_OUTBOX);
    }

    public function getArchiveAction()
    {
        $messages = $this->repos()->getInboxRepository()
                  ->findArchive(
                      $this->getUser()
                  );
        return $this->displayList($messages, Inbox::DIRECTORY_ARCHIVE);
    }

    protected function displayList(array $messages, $directory)
    {
        return [
            'messages' => !empty($messages) ? $messages : [],
        ];
    }

    /**
     * @ParamConverter("message", class="Dba\GameBundle\Entity\Inbox")
     */
    public function postArchiveAction(Inbox $message)
    {
        if (in_array(
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
        return [];
    }

    /**
     * @ParamConverter("message", class="Dba\GameBundle\Entity\Inbox")
     */
    public function getReadAction(Inbox $message)
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

        return [
            'message' => $message,
        ];
    }

    public function postWriteAction(Request $request)
    {
        return $this->write($request);
    }

    /**
     * @ParamConverter("message", class="Dba\GameBundle\Entity\Inbox")
     */
    public function postWriteReplyAction(Request $request, Inbox $message = null)
    {
        return $this->write($request, $message);
    }

    /**
     * @ParamConverter("message", class="Dba\GameBundle\Entity\Inbox")
     */
    public function postWriteGuildAction(Request $request, Inbox $message = null)
    {
        return $this->write($request, $message, null, true);
    }

    /**
     * @ParamConverter("player", class="Dba\GameBundle\Entity\Player", options={"id" = "playerId"})
     */
    public function postWritePlayerAction(Request $request, Player $player)
    {
        return $this->write($request, null, $player);
    }

    protected function write(Request $request, Inbox $message = null, Player $player = null, $guild = null)
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
        if (!$form->isSubmitted() && !$form->isValid()) {
            return $this->badRequest($this->getErrorMessages($form));
        }

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
        }

        var_dump($recipients);
        return $this->createdRequest();
    }

    public function postClearAction($what)
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
        return [];
    }

    public function deleteMessageAction(Inbox $message)
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
        return [];
    }
}
