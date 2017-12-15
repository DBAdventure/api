<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Form\PlayerAppearance;
use Dba\GameBundle\Form\PlayerSettings;

/**
 * @Annotations\NamePrefix("account_")
 */
class AccountController extends BaseController
{
    const TRAINING_ROOM_ACTION = 5;
    const TRAINING_ROOM_SKILL = 1;

    const AC = 'accuracy';
    const AG = 'agility';
    const AN = 'analysis';
    const HE = 'health';
    const IN = 'intellect';
    const KI = 'ki';
    const RE = 'resistance';
    const SK = 'skill';
    const ST = 'strength';
    const VI = 'vision';
    const AVAILABLE_SKILLS = [
        self::AC,
        self::AG,
        self::AN,
        self::HE,
        self::IN,
        self::KI,
        self::RE,
        self::SK,
        self::ST,
        self::VI,
    ];

    /**
     * @Annotations\Get("/player")
     */
    public function getPlayerAction()
    {
        $player = $this->getUser();
        $roleService = $this->get('dba.admin.role');

        $miniMapObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $this->getUser(),
                'object' => $this->repos()->getObjectRepository()->findOneBy(
                    [
                        'id' => Object::DEFAULT_MAP,
                        'enabled' => true,
                    ]
                ),
            ]
        );

        return [
            'id' => $player->getId(),
            'username' => $player->getUsername(),
            'email' => $player->getEmail(),
            'name' => $player->getName(),
            'history' => $player->getHistory(),
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
            'battle_points_remaining_start' => $player->getBattlePointsRemaining($player->getLevel() - 1),
            'battle_points_remaining_end' => (
                $player->getBattlePointsRemaining() - $player->getBattlePointsRemaining($player->getLevel() - 1)
            ),
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
            'objects' => [
                'strength' => $player->getObjectsStrength(),
                'accuracy' => $player->getObjectsAccuracy(),
                'agility' => $player->getObjectsAgility(),
                'analysis' => $player->getObjectsAnalysis(),
                'skill' => $player->getObjectsSkill(),
                'intellect' => $player->getObjectsIntellect(),
                'resistance' => $player->getObjectsResistance(),
                'vision' => $player->getObjectsVision(),
                'max_health' => $player->getObjectsMaxHealth(),
                'max_ki' => $player->getObjectsMaxKi(),
            ],
            'last_login' => $player->getLastLogin(),
            'roles' => $player->getRoles(),
            'class' => $player->getClass(),
            'guild_player' => $player->getGuildPlayer(),
            'map' => $player->getMap(),
            'case' => $this->repos()->getMapRepository()->getCaseData(
                $player,
                $this->services()->getTemplateService()->getPeriod()
            ),
            'rank' => $player->getRank(),
            'side' => $player->getSide(),
            'race' => $player->getRace(),
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
            'has_mini_map' => !empty($miniMapObject) && $miniMapObject->getNumber() == 1,
            'is_admin' => $roleService->isGranted(Player::ROLE_MODO, $player),
        ];
    }

    /**
     * @Annotations\Get("/events", name="_account")
     */
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

    /**
     * @Annotations\Post("/settings")
     */
    public function settingsAction(Request $request)
    {
        $player = $this->getUser();
        $backup = [
            'email' => $player->getEmail(),
            'username' => $player->getUsername(),
            'password' => $player->getPassword(),
            'history' => $player->getHistory(),
        ];

        $form = $this->createForm(PlayerSettings::class, $player);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $player->setEmail($backup['email']);
            $player->setPassword($backup['password']);
            $player->setUsername($backup['username']);
            $player->setHistory($backup['history']);
            $this->em()->persist($player);
            $this->em()->flush();
            return $this->badRequest();
        }

        if (empty($player->getPassword())) {
            $player->setPassword($backup['password']);
        } else {
            $player->setPassword(
                $this->container->get('security.password_encoder')->encodePassword(
                    $player,
                    $player->getPassword()
                )
            );
        }

        $this->em()->persist($player);
        $this->em()->flush();

        return [];
    }

    /**
     * @Annotations\Post("/appearance")
     */
    public function appearanceAction(Request $request)
    {
        $player = $this->getUser();
        $imageBackup = $player->getImage();
        $form = $this->createForm(PlayerAppearance::class, $player);
        $form->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            $player->setImage($imageBackup);
            $this->em()->persist($player);
            $this->em()->flush();
            return $this->badRequest();
        }

        return [];
    }

    /**
     * @Annotations\Post("/confirm/{id}/{token}")
     */
    public function postConfirmUserTokenAction($id, $token)
    {
        $player = $this->repos()->getPlayerRepository()->findOneByConfirmationToken($token);

        if (null === $player || $player->getId() != $id) {
            return $this->badRequest();
        }

        $player->setConfirmationToken(null);
        $player->setEnabled(true);
        // @TODO In progress
        // $this->services()->getPlayerService()->prepareTutorial($player);
        $this->em()->persist($player);
        $this->em()->flush();

        return [];
    }

    /**
     * @Annotations\Post("/training/{what}")
     */
    public function postTrainAction($what)
    {
        $player = $this->getUser();
        if (empty($what) ||
            !in_array($what, self::AVAILABLE_SKILLS) ||
            $player->getActionPoints() < self::TRAINING_ROOM_ACTION ||
            $player->getSkillPoints() < self::TRAINING_ROOM_SKILL
        ) {
            return $this->badRequest();
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
            case self::AC:
            case self::AG:
            case self::AN:
            case self::IN:
            case self::RE:
            case self::SK:
            case self::ST:
            case self::VI:
                $currentValue = call_user_func([$player, 'get'. ucfirst($what)]);
                call_user_func([$player, 'set' . ucfirst($what)], $currentValue + 1);
                break;
        }

        $player->reloadBonus();
        $player->usePoints(Player::ACTION_POINT, self::TRAINING_ROOM_ACTION);
        $player->usePoints(Player::SKILL_POINT, self::TRAINING_ROOM_SKILL);

        $this->em()->persist($player);
        $this->em()->flush();

        return [];
    }
}
