<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Guild;
use Dba\GameBundle\Entity\GuildPlayer;
use Dba\GameBundle\Entity\GuildRank;
use Dba\GameBundle\Form;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/guild")
 */
class GuildController extends BaseController
{
    const CREATE_GUILD_AMOUNT = 200;

    /**
     * @Route("", name="guild", methods="GET")
     * @Template()
     */
    public function indexAction()
    {
        $guild = $this->getUser()->getGuildPlayer();
        if (empty($guild)) {
            return $this->render('DbaGameBundle::guild/no-guild.html.twig');
        }

        return $this->render('DbaGameBundle::guild/index.html.twig');
    }

    /**
     * @Route("/view/{id}", name="guild.view", methods="GET", requirements={"id": "\d+"}, defaults={"id" : null})
     * @Template()
     */
    public function viewAction($id)
    {
        $guildRepo = $this->repos()->getGuildRepository();
        return $this->render(
            'DbaGameBundle::guild/view.html.twig',
            [
                'guilds' => $guildRepo->findByEnabled(true),
                'selectedGuild' => !empty($id) ? $guildRepo->findOneBy(
                    [
                        'id' => $id,
                        'enabled' => true
                    ]
                ) : null
            ]
        );
    }

    /**
     * @Route("/create", name="guild.create", methods={"GET", "POST"})
     * @Template()
     */
    public function createAction(Request $request)
    {
        $player = $this->getUser();
        if (!empty($player->getGuildPlayer()) || $player->getZeni() < self::CREATE_GUILD_AMOUNT) {
            return $this->forbidden();
        }

        $guild = new Guild();
        $form = $this->createForm(Form\GuildCreate::class, $guild);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $guildRank = new GuildRank();
            $guildRank->setName('member');
            $guildRank->setGuild($guild);
            $guildRank->setRole(GuildRank::ROLE_PLAYER);
            $this->em()->persist($guildRank);

            $guildRank = new GuildRank();
            $guildRank->setName('moderator');
            $guildRank->setGuild($guild);
            $guildRank->setRole(GuildRank::ROLE_MODO);
            $this->em()->persist($guildRank);

            $guildRank = new GuildRank();
            $guildRank->setName('administrator');
            $guildRank->setGuild($guild);
            $guildRank->setRole(GuildRank::ROLE_ADMIN);
            $this->em()->persist($guildRank);

            $guildPlayer = new GuildPlayer();
            $guildPlayer->setPlayer($player);
            $guildPlayer->setGuild($guild);
            $guildPlayer->setEnabled(true);
            $guildPlayer->setZeni(0);
            $guildPlayer->setRank($guildRank);

            $player->setZeni($player->getZeni() - self::CREATE_GUILD_AMOUNT);

            $guild->setCreatedBy($player);
            $guild->setEnabled(false);

            $this->em()->persist($player);
            $this->em()->persist($guildPlayer);
            $this->em()->persist($guild);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('guild.created')
            );

            return $this->redirect($this->generateUrl('guild'));
        }

        return $this->render(
            'DbaGameBundle::guild/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/members/{type}", name="guild.members", methods="GET", defaults={"type" : null})
     * @Template()
     */
    public function membersAction($type)
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (empty($guildPlayer) or !$guildPlayer->isEnabled()) {
            return $this->forbidden();
        }

        $players = $guildPlayer->getGuild()->getPlayers();
        $guildPlayers = [];
        foreach ($players as $player) {
            if (($type == 'requester' && $player->isEnabled()) || ($type != 'requester' && !$player->isEnabled())) {
                continue;
            }
            $guildPlayers[] = $player;
        }

        return $this->render(
            'DbaGameBundle::guild/members.html.twig',
            [
                'guildPlayers' => $guildPlayers
            ]
        );
    }

    /**
     * @Route("/events", name="guild.events", methods="GET")
     * @Template()
     */
    public function eventsAction()
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (empty($guildPlayer) or !$guildPlayer->isEnabled()) {
            return $this->forbidden();
        }

        return $this->render(
            'DbaGameBundle::guild/events.html.twig'
        );
    }

    /**
     * @Route("/join/{id}", name="guild.join", methods="GET", requirements={"id": "\d+"})
     * @ParamConverter("guild", class="Dba\GameBundle\Entity\Guild")
     * @Template()
     */
    public function joinAction(Guild $guild)
    {
        $player = $this->getUser();
        if (!empty($player->getGuildPlayer())) {
            $this->addFlash(
                'danger',
                $this->trans('guild.already.have')
            );

            return $this->redirect($this->generateUrl('guild'));
        }

        $guildRank = $this->repos()->getGuildRankRepository()
                   ->findOneBy(['role' => GuildRank::ROLE_PLAYER]);

        $guildPlayer = new GuildPlayer();
        $guildPlayer->setPlayer($player);
        $guildPlayer->setGuild($guild);
        $guildPlayer->setEnabled(false);
        $guildPlayer->setZeni(0);
        $guildPlayer->setRank($guildRank);

        $this->em()->persist($guildPlayer);

        $this->em()->flush();
        $this->addFlash(
            'danger',
            $this->trans('guild.joined')
        );

        return $this->redirect($this->generateUrl('guild'));
    }
}
