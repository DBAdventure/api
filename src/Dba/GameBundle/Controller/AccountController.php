<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Form\PlayerAppearance;
use Dba\GameBundle\Entity\Player;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/account")
 */
class AccountController extends BaseController
{
    const TRAINING_ROOM_ACTION = 5;
    const TRAINING_ROOM_SKILL = 1;

    const ST = 'strength';
    const AC = 'accuracy';
    const SK = 'skill';
    const AG = 'agility';
    const VI = 'vision';
    const IN = 'intellect';
    const RE = 'resistance';
    const HE = 'health';
    const KI = 'ki';
    const AVAILABLE_SKILLS = [
        self::ST,
        self::AC,
        self::SK,
        self::AG,
        self::VI,
        self::IN,
        self::RE,
        self::HE,
        self::KI
    ];

    /**
     * @Route("/appearance", name="account.appearance", methods={"GET", "POST"})
     * @Template()
     */
    public function appearanceAction(Request $request)
    {
        $player = $this->getUser();
        $form = $this->createForm(PlayerAppearance::class, $player);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em()->persist($player);
                $this->em()->flush();
            } else {
                $this->addFlash(
                    'danger',
                    $this->trans('account.appearance.failed')
                );
            }

            return $this->redirect($this->generateUrl('account.appearance'));
        }

        return $this->render(
            'DbaGameBundle::account/appearance.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/profile", name="account.profile", methods="GET")
     * @Template()
     */
    public function profileAction()
    {
        return $this->render('DbaGameBundle::account/profile.html.twig');
    }

    /**
     * @Route("/dashboard", name="account.dashboard", methods="GET")
     * @Template()
     */
    public function dashboardAction()
    {
        return $this->render(
            'DbaGameBundle::account/dashboard.html.twig'
        );
    }

    /**
     * @Route("/training/room/{what}", name="account.training.room", methods="GET", defaults={"what" : null})
     * @Template()
     */
    public function trainingRoomAction($what)
    {
        $player = $this->getUser();
        if (!empty($what) && in_array($what, self::AVAILABLE_SKILLS)) {
            if ($player->getActionPoints() < self::TRAINING_ROOM_ACTION ||
                $player->getSkillPoints() < self::TRAINING_ROOM_SKILL
            ) {
                $what = null;
            }

            switch ($what) {
                case self::HE:
                    $player->setMaxHealth($player->getMaxHealth() + 45);
                    $player->setHealth($player->getHealth() + 45);
                    break;
                case self::KI:
                    $player->setMaxKi($player->getMaxKi() + 2);
                    $player->setKi($player->getKi() + 2);
                    break;
                case self::ST:
                case self::AC:
                case self::SK:
                case self::AG:
                case self::VI:
                case self::IN:
                case self::RE:
                    $currentValue = call_user_func([$player, 'get'. ucfirst($what)]);
                    call_user_func([$player, 'set' . ucfirst($what)], $currentValue + 1);
                    break;
                default:
                    $this->addFlash(
                        'danger',
                        $this->trans('training.error')
                    );
                    return $this->redirect($this->generateUrl('account.training.room'));
            }

            $player->reloadBonus();
            $player->usePoints(Player::ACTION_POINT, self::TRAINING_ROOM_ACTION);
            $player->usePoints(Player::SKILL_POINT, self::TRAINING_ROOM_SKILL);

            $this->em()->persist($player);
            $this->em()->flush();
            $this->addFlash(
                'success',
                $this->trans('training.success')
            );
            return $this->redirect($this->generateUrl('account.training.room'));
        }

        return $this->render('DbaGameBundle::account/training.room.html.twig');
    }
}
