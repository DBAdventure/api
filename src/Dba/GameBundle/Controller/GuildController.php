<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dba\GameBundle\Entity\Guild;
use Dba\GameBundle\Entity\GuildPlayer;
use Dba\GameBundle\Entity\GuildRank;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Form;


class GuildController extends BaseController
{
    const CREATE_GUILD_AMOUNT = 200;

    /**
     * @Annotations\View(serializerGroups={"Default", "GuildView"})
     * @Annotations\Get("/list")
     */
    public function getListAction()
    {
        return [
            'guilds' => $this->repos()->getGuildRepository()->findByEnabled(true),
        ];
    }

    /**
     * @Annotations\Post("/create")
     */
    public function postCreateAction(Request $request)
    {
        $player = $this->getUser();
        if (!empty($player->getGuildPlayer()) || $player->getZeni() < self::CREATE_GUILD_AMOUNT) {
            return $this->forbidden();
        }

        $guild = new Guild();
        $form = $this->createForm(Form\GuildCreate::class, $guild);
        $form->handleRequest($request);
        if (!$form->isSubmitted() ||! $form->isValid()) {
            return $this->badRequest($this->getErrorMessages($form));
        }

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

        return [];
    }

    /**
     * @Annotations\View(serializerGroups={"Default", "Guild"})
     * @Annotations\Get("/members")
     * @Annotations\Get("/members/{type}", name="_requester")
     */
    public function getMembersAction($type = null)
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

        return [
            'players' => $guildPlayers
        ];
    }

    /**
     * @Annotations\Get("/events", name="_guild")
     */
    public function getEventsAction()
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (empty($guildPlayer) or !$guildPlayer->isEnabled()) {
            return $this->forbidden();
        }

        return [];
    }

    /**
     * @Annotations\Post("/join/{id}")
     * @ParamConverter("guild", class="Dba\GameBundle\Entity\Guild")
     */
    public function postJoinAction(Guild $guild)
    {
        $player = $this->getUser();
        if (!empty($player->getGuildPlayer()) || count($guild->getPlayers()) >= Guild::MAX_MEMBERS) {
            return $this->forbidden();
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

        return [];
    }
}
