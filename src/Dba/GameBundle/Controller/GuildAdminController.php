<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Dba\GameBundle\Entity\Guild;
use Dba\GameBundle\Entity\GuildPlayer;
use Dba\GameBundle\Entity\GuildRank;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Form\GuildSettings;

/**
 * @Annotations\NamePrefix("guild_admin_")
 */
class GuildAdminController extends BaseController
{
    protected function checkRank(GuildPlayer $guildPlayer, $rank)
    {
        if (empty($guildPlayer) || !$guildPlayer->isEnabled() || !$guildPlayer->getGuild()->isEnabled()) {
            return false;
        }

        if ($rank == GuildRank::ROLE_MODO) {
            return $guildPlayer->getRank()->isModo() || $guildPlayer->getRank()->isAdmin();
        }

        return $guildPlayer->getRank()->isAdmin();
    }

    protected function accessDenied()
    {
        $this->services()->getGuildService()->addEvent(
            $this->getUser(),
            $this->getUser()->getGuildPlayer()->getGuild(),
            'event.guild.admin.access.denied'
        );

        return $this->forbidden('guild.admin.access.denied');
    }

    /**
     * @Annotations\Post("/requester/{id}")
     * @ParamConverter("targetPlayer", class="Dba\GameBundle\Entity\GuildPlayer")
     */
    public function requesterAction(Request $request, GuildPlayer $targetPlayer)
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_MODO)) {
            return $this->accessDenied();
        }

        if ($targetPlayer->isEnabled()) {
            return $this->badRequest('guild.admin.bad.request');
        }

        $decision = $request->request->get('decision', false);
        if ($decision) {
            $targetPlayer->setEnabled(true);
            $this->em()->persist($targetPlayer);
            $message = 'guild.admin.requester.accepted';
            $eventMessage = 'event.guild.admin.request.accept';
        } else {
            $this->em()->remove($targetPlayer);
            $message = 'guild.admin.requester.declined';
            $eventMessage = 'event.guild.admin.request.decline';
        }

        $this->services()->getGuildService()->addEvent(
            $this->getUser(),
            $guildPlayer->getGuild(),
            $eventMessage,
            [
                'name' => $targetPlayer->getPlayer()->getName(),
            ]
        );
        $this->em()->flush();

        return [
            'message' => $message,
        ];
    }

    /**
     * @Annotations\Post("/fired/{id}")
     * @ParamConverter("targetPlayer", class="Dba\GameBundle\Entity\GuildPlayer")
     */
    public function firedAction(GuildPlayer $targetPlayer)
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_MODO)) {
            return $this->forbidden();
        }

        if (!$targetPlayer->isEnabled()) {
            return $this->badRequest('guild.admin.bad.request');
        }

        $playerName = $targetPlayer->getPlayer()->getName();
        $this->services()->getGuildService()->addEvent(
            $this->getUser(),
            $guildPlayer->getGuild(),
            'event.guild.admin.fired',
            [
                'name' => $playerName,
            ]
        );

        $this->em()->remove($targetPlayer);
        $this->em()->flush();

        return [
            'message' => 'guild.admin.fired',
            'parameters' => [
                'name' => $playerName,
            ],
        ];
    }

    /**
     * @Annotations\Post("/ranks")
     * @Annotations\Post("/ranks/{id}", name="_set")
     * @ParamConverter("targetPlayer", class="Dba\GameBundle\Entity\GuildPlayer", isOptional=true)
     */
    public function rankAction(Request $request, GuildPlayer $targetPlayer = null)
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_ADMIN)) {
            return $this->forbidden();
        }

        if (empty($targetPlayer)) {
            $ranks = $request->request->get('guild_ranks', []);
            $this->editRankRole(
                $guildPlayer->getGuild(),
                GuildRank::ROLE_PLAYER,
                $ranks
            );
            $this->editRankRole(
                $guildPlayer->getGuild(),
                GuildRank::ROLE_MODO,
                $ranks
            );
            $this->editRankRole(
                $guildPlayer->getGuild(),
                GuildRank::ROLE_ADMIN,
                $ranks
            );

            return [];
        }

        $rank = $request->request->get('what');
        if (empty($rank) || !in_array($rank, [GuildRank::ROLE_ADMIN, GuildRank::ROLE_MODO, GuildRank::ROLE_PLAYER])) {
            return $this->badRequest();
        }

        $newRank = $this->repos()->getGuildRankRepository()->findOneByRole($rank);
        $targetPlayer->setRank($newRank);
        $this->em()->persist($targetPlayer);
        $this->em()->flush();

        return [];
    }

    protected function editRankRole($guild, $role, $ranks)
    {
        if (!empty($ranks[$role]) && !empty(trim($ranks[$role]))) {
            $guildRank = $this->repos()->getGuildRankRepository()->findOneBy([
                'guild' => $guild,
                'role' => $role
            ]);
            $guildRank->setName($ranks[$role]);
            $this->em()->persist($guildRank);
            $this->em()->flush();
        }
    }

    /**
     * @Annotations\Post("/general/{id}")
     * @Annotations\Get("/general")
     */
    public function generalAction()
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_ADMIN)) {
            return $this->forbidden();
        }

        return $this->render('DbaGameBundle::guild/admin/index.html.twig');
    }

    /**
     * @Annotations\Post("/settings")
     */
    public function settingsAction(Request $request)
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_MODO)) {
            return $this->forbidden();
        }

        $guild = $guildPlayer->getGuild();
        $form = $this->createForm(GuildSettings::class, $guild);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->badRequest();
        }

        $this->services()->getGuildService()->addEvent(
            $this->getUser(),
            $guildPlayer->getGuild(),
            'event.guild.admin.settings'
        );
        $this->em()->persist($guild);
        $this->em()->flush();

        return [];
    }
}
