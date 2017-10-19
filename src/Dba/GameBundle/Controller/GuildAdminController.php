<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Guild;
use Dba\GameBundle\Entity\GuildPlayer;
use Dba\GameBundle\Entity\GuildRank;
use Dba\GameBundle\Form;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

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
        $this->addFlash(
            'danger',
            $this->trans('guild.admin.access.denied')
        );
        return $this->redirect($this->generateUrl('guild'));
    }

    /**
     * @Route("", name="guild.admin", methods="GET")
     * @Template()
     */
    public function indexAction()
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_MODO)) {
            return $this->accessDenied();
        }

        return $this->render('DbaGameBundle::guild/admin/index.html.twig');
    }

    /**
     * @Route("/requester", name="guild.admin.requester", methods="GET")
     * @Route("/requester/{id}/choice/{decision}", name="guild.admin.requester.decision",
              methods="GET", requirements={"id": "\d+", "decision": "(accept|decline)"})
     * @ParamConverter("targetPlayer", class="Dba\GameBundle\Entity\GuildPlayer",
                       isOptional="true", options={"id" = "id"})
     * @Template()
     */
    public function requesterAction(GuildPlayer $targetPlayer = null, $decision = null)
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_MODO)) {
            return $this->accessDenied();
        }

        if (!empty($targetPlayer)) {
            if ($targetPlayer->isEnabled()) {
                return $this->accessDenied();
            }

            if ($decision == 'accept') {
                $targetPlayer->setEnabled(true);
                $this->em()->persist($targetPlayer);
                $this->em()->flush();
                $this->addFlash(
                    'success',
                    $this->trans('guild.admin.requester.accept')
                );
                return $this->redirect($this->generateUrl('guild.admin'));
            }

            $this->em()->remove($targetPlayer());
            $this->em()->flush();
            $this->addFlash(
                'success',
                $this->trans('guild.admin.requester.decline')
            );
            return $this->redirect($this->generateUrl('guild.admin'));
        }

        $players = $guildPlayer->getGuild()->getPlayers();
        $guildPlayers = [];
        foreach ($players as $player) {
            if ($player->isEnabled()) {
                continue;
            }

            $guildPlayers[] = $player;
        }

        return $this->render(
            'DbaGameBundle::guild/admin/requester.html.twig',
            [
                'guildPlayers' => $guildPlayers
            ]
        );
    }

    /**
     * @Route("/fired/{id}", name="guild.admin.fired", methods="GET",
              defaults={"id": null}, requirements={"id": "\d+"})
     * @ParamConverter("targetPlayer", class="Dba\GameBundle\Entity\GuildPlayer",
                       isOptional="true", options={"id" = "id"})
     * @Template()
     */
    public function firedAction()
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_MODO)) {
            return $this->forbidden();
        }

        $players = $guildPlayer->getGuild()->getPlayers();
        $guildPlayers = [];
        foreach ($players as $player) {
            if (!$player->isEnabled() or $player->getRank()->isAdmin()) {
                continue;
            }

            $guildPlayers[] = $player;
        }

        return $this->render(
            'DbaGameBundle::guild/admin/fired.html.twig',
            [
                'guildPlayers' => $guildPlayers
            ]
        );
    }

    /**
     * @Route("/rank", name="guild.admin.rank", methods="GET")
     * @Template()
     */
    public function rankAction()
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_ADMIN)) {
            return $this->forbidden();
        }

        return $this->render('DbaGameBundle::guild/admin/index.html.twig');
    }

    /**
     * @Route("/general", name="guild.admin.general", methods="GET")
     * @Template()
     */
    public function generalAction()
    {
        $guildPlayer = $this->getUser()->getGuildPlayer();
        if (!$this->checkRank($guildPlayer, GuildRank::ROLE_ADMIN)) {
            return $this->forbidden();
        }

        return $this->render('DbaGameBundle::guild/admin/index.html.twig');
    }
}
