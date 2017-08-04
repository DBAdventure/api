<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Form\PlayerAppearance;
use Dba\GameBundle\Entity\Player;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

    public function getPlayerAction()
    {
        if (!$this->getUser()) {
            return $this->forbidden();
        }

        $player = $this->getUser();
        return [
            'id' => $player->getId(),
            'username' => $player->getUsername(),
            'email' => $player->getEmail(),
            'name' => $player->getName(),
            'image' => $player->getImage(),
            'x' => $player->getX(),
            'y' => $player->getY(),
            'side_points' => $player->getSidePoints(),
            'action_points' => $player->getActionPoints(),
            'max_action_points' => $player->getMaxActionPoints(),
            'fatigue_points' => $player->getFatiguePoints(),
            'max_fatigue_points' => $player->getMaxFatiguePoints(),
            'movement_points' => $player->getMovementPoints(),
            'max_movement_points' => $player->getMaxMovementPoints(),
            'battle_points' => $player->getBattlePoints(),
            'skill_points' => $player->getSkillPoints(),
            'created_at' => $player->getCreatedAt(),
            'updated_at' => $player->getUpdatedAt(),
            'time_remainning' => [
                'action_points' => $player->getTimeRemaining(Player::ACTION_POINT),
                'fatigue_points' => $player->getTimeRemaining(Player::FATIGUE_POINT),
                'movement_points' => $player->getTimeRemaining(Player::MOVEMENT_POINT),
                'ki_points' => $player->getTimeRemaining(Player::KI_POINT),
            ],
            'zeni' => $player->getZeni(),
            'level' => $player->getLevel(),
            'total_strength' => $player->getTotalStrength(),
            'total_accuracy' => $player->getTotalAccuracy(),
            'total_agility' => $player->getTotalAgility(),
            'total_analysis' => $player->getTotalAnalysis(),
            'total_skill' => $player->getTotalSkill(),
            'total_intellect' => $player->getTotalIntellect(),
            'total_resistance' => $player->getTotalResistance(),
            'total_vision' => $player->getTotalVision(),
            'total_max_ki' => $player->getTotalMaxKi(),
            'total_max_health' => $player->getTotalMaxHealth(),
            'strength' => $player->getStrength(),
            'accuracy' => $player->getAccuracy(),
            'agility' => $player->getAgility(),
            'analysis' => $player->getAnalysis(),
            'skill' => $player->getSkill(),
            'intellect' => $player->getIntellect(),
            'resistance' => $player->getResistance(),
            'vision' => $player->getVision(),
            'max_ki' => $player->getMaxKi(),
            'max_health' => $player->getMaxHealth(),
            'health' => $player->getHealth(),
            'ki' => $player->getKi(),
            'last_login' => $player->getLastLogin(),
            'roles' => $player->getRoles(),
            'class' => $player->getClass(),
            'map' => $player->getMap(),
            'rank' => $player->getRank(),
            'side' => $player->getSide(),
            'target' => $player->getTarget(),
            'stats' => [
                'death_count' => $player->getDeathCount(),
                'nb_kill_good' => $player->getNbKillGood(),
                'nb_hit_good' => $player->getNbHitGood(),
                'nb_damage_good' => $player->getNbDamageGood(),
                'nb_kill_bad' => $player->getNbKillBad(),
                'nb_hit_bad' => $player->getNbHitBad(),
                'nb_damage_bad' => $player->getNbDamageBad(),
                'nb_kill_npc' => $player->getNbKillNpc(),
                'nb_hit_npc' => $player->getNbHitNpc(),
                'nb_damage_npc' => $player->getNbDamageNpc(),
                'nb_kill_hq' => $player->getNbKillHq(),
                'nb_hit_hq' => $player->getNbHitHq(),
                'nb_damage_hq' => $player->getNbDamageHq(),
                'nb_stolen_zeni' => $player->getNbStolenZeni(),
                'nb_action_stolen_zeni' => $player->getNbActionStolenZeni(),
                'nb_dodge' => $player->getNbDodge(),
                'nb_wanted' => $player->getNbWanted(),
                'nb_analysis' => $player->getNbAnalysis(),
                'nb_spell' => $player->getNbSpell(),
                'nb_health_given' => $player->getNbHealthGiven(),
                'nb_total_health_given' => $player->getNbTotalHealthGiven(),
                'nb_slap_taken' => $player->getNbSlapTaken(),
                'nb_slap_given' => $player->getNbSlapGiven(),
            ],
            'betrayals' => $player->getBetrayals(),
            'head_price' => $player->getHeadPrice(),
            'inventory_max_weight' => $player->getInventoryMaxWeight(),
            'inventory_weight' => $player->getInventoryWeight(),
        ];
    }

    public function getEventsAction()
    {
        $repos = $this->repos()->getPlayerEventRepository();
        return [
            'player' => $repos->findBy(
                ['player' => $this->getUser()],
                ['createdAt' => 'DESC'],
                10
            ),
            'target' => $repos->findBy(
                ['target' => $this->getUser()],
                ['createdAt' => 'DESC'],
                10
            ),
        ];
    }

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
     * @Route("/confirm/{id}/{token}", name="confirm", methods={"GET"})
     * @Template()
     */
    public function confirmAction($id, $token)
    {
        $playerRepo = $this->repos()->getPlayerRepository();
        $player = $playerRepo->findOneByConfirmationToken($token);

        if (null === $player || $player->getId() != $id) {
            throw new NotFoundHttpException(sprintf('The player with confirmation token "%s" does not exist', $token));
        }

        $player->setConfirmationToken(null);
        $player->setEnabled(true);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->addFlash(
            'success',
            $this->trans('account.enabled')
        );

        return $this->redirect($this->generateUrl('home'));
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
