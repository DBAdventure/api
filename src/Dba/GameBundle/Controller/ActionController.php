<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Dba\GameBundle\Event\ActionEvent;
use Dba\GameBundle\Event\DbaEvents;
use Dba\GameBundle\Entity\MapBonus;
use Dba\GameBundle\Entity\MapObject;
use Dba\GameBundle\Entity\MapObjectType;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\PlayerSpell;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Entity\Spell;

/**
 * @Annotations\NamePrefix("action_")
 */
class ActionController extends BaseController
{
    const DEFAULT_BATTLE_POINT_KILL = 20;
    const DEFAULT_BATTLE_POINT_KILL_SLAP = 10;
    const DEFAULT_NPC_ZENI = 50;

    /**
     * @Annotations\Post("/convert")
     */
    public function postConvertAction()
    {
        $player = $this->getUser();
        if (!$player->canConvert()) {
            return $this->forbidden();
        }

        $player->usePoints(Player::ACTION_POINT, 20);
        $player->addPoints(Player::MOVEMENT_POINT, 40);
        $this->em()->persist($player);
        $this->em()->flush();

        return [];
    }

    /**
     * @Annotations\Post("/move/{where}")
     */
    public function postMoveAction($where)
    {
        $player = $this->getUser();
        if ($player->getMovementPoints() < Player::MOVEMENT_ACTION ||
            !in_array($where, Player::AVAILABLE_MOVE)
        ) {
            // failed to move
            return $this->forbidden();
        }

        $this->dispatchEvent(DbaEvents::BEFORE_MOVE, $player, null, ['where' => $where]);
        list($result, $move) = $this->services()->getPlayerService()->move($player, $where);
        if (empty($result)) {
            return $this->forbidden();
        }

        $player->usePoints(Player::MOVEMENT_POINT, Player::MOVEMENT_ACTION + (int) ($move > 1));
        $this->dispatchEvent(DbaEvents::AFTER_MOVE, $player, null);

        $this->em()->persist($player);
        $this->em()->flush();

        return [];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @Annotations\Post("/attack/{target}", name="_normal")
     * @Annotations\Post("/attack/{target}/{type}", name="_type"))
     */
    public function postAttackAction(Player $target, $type = null)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $target, Player::ATTACK_ACTION) ||
            $player->getId() == $target->getId() ||
            !$this->canMakeAction($player, $target)
        ) {
            // failed to attack
            return $this->forbidden();
        }

        if ($type == Player::ATTACK_TYPE_REVENGE && $player->getTarget()->getId() != $target->getId()) {
            // Attack is just normal can't revenge
            $type = null;
        } elseif ($type == Player::ATTACK_TYPE_BETRAY && $player->getSide()->getId() != $target->getSide()->getId()) {
            // Attack is just normal can't betray
            $type = null;
        } elseif (empty($type) && $player->getSide()->getId() == $target->getSide()->getId()) {
            $type = Player::ATTACK_TYPE_BETRAY;
        }

        $playerService = $this->services()->getPlayerService();
        if ($return = $playerService->protectLowLevel($player, $target)) {
            $this->em()->persist($player);
            $this->em()->flush();

            return [
                'messages' => $return,
                'target' => $target,
                'attackType' => $type,
                'isDead' => false
            ];
        }

        $messages = [];
        $this->dispatchEvent(DbaEvents::BEFORE_ATTACK, $player, $target, ['type' => $type, 'messages' => &$messages]);
        list($luck, $damages, $isDead) = $playerService->attack($player, $target);

        if ($type == Player::ATTACK_TYPE_BETRAY) {
            $messages[] = $this->trans('action.betray');
        } elseif ($type == Player::ATTACK_TYPE_REVENGE) {
            $messages[] = $this->trans('action.revenge');
        }

        if (($luck >= -4 && $luck < -3) || $damages <= 0) {
            $messages[] = $this->trans('action.attack.dodge');
            $eventMessage = 'event.action.attack.dodge';
        } elseif ($luck >= -3 && $luck < -2.25) {
            $messages[] = $this->trans('action.attack.really.failed');
            $eventMessage = 'event.action.attack.really.failed';
        } elseif ($luck >= -2.25 && $luck < -1.5) {
            $messages[] = $this->trans('action.attack.failed');
            $eventMessage = 'event.action.attack.failed';
        } elseif ($luck >= -1.5 && $luck < 0.5) {
            $messages[] = $this->trans('action.attack.mediocre');
            $eventMessage = 'event.action.attack.moderately.succeeded';
        } elseif ($luck >= 0.5 && $luck < 1) {
            $messages[] = $this->trans('action.attack.successful');
            $eventMessage = 'event.action.attack.succeeded';
        } elseif ($luck >= 1 && $luck < 1.5) {
            $messages[] = $this->trans('action.attack.nice');
            $eventMessage = 'event.action.attack.nice';
        } elseif ($luck >= 1.5 && $luck < 5) {
            $messages[] = $this->trans('action.attack.critical');
            $eventMessage = 'event.action.attack.critic';
        } elseif ($luck >= 5 && $luck <= 6) {
            $messages[] = $this->trans('action.attack.master');
            $eventMessage = 'event.action.attack.master';
        }

        $messages[] = $this->trans('action.attack.damages', ['%damages%' => $damages]);
        if ($type == Player::ATTACK_TYPE_BETRAY) {
            $messages[] = $this->trans('action.betrayPoints');
        }
        $playerService->addEvent(
            $player,
            $target,
            $eventMessage,
            [
                'damages' => $damages
            ]
        );

        $playerService->addBattlePoints($player, Player::ATTACK_ACTION, $target, $messages);

        if ($isDead) {
            $messages[] = $this->trans('action.killed');
            if (!$target->isPlayer()) {
                $target->setZeni($target->getZeni() + self::DEFAULT_NPC_ZENI);
            } else {
                $playerService->addEvent(
                    $player,
                    $target,
                    'event.action.attack.killed'
                );
            }

            $this->checkHeadPrice($player, $target, $messages);
            $messages[] = $this->trans('action.attack.kill.battlePoints');
            $player->setBattlePoints($player->getBattlePoints() + self::DEFAULT_BATTLE_POINT_KILL);

            if ($type == Player::ATTACK_TYPE_REVENGE) {
                $player->setTarget(null);
                $messages[] = $this->trans('action.attack.kill.revenge');
            }
        }

        $playerService->addAttackStats($player, $target, $damages, $isDead, $type);
        $player->usePoints(Player::ACTION_POINT, Player::ATTACK_ACTION);
        $this->dispatchEvent(
            DbaEvents::AFTER_ATTACK,
            $player,
            $target,
            [
                'type' => $type,
                'messages' => &$messages,
                'isDead' => $isDead,
                'damages' => $damages
            ]
        );

        $this->em()->persist($player);
        $this->em()->persist($target);
        $this->em()->flush();

        return [
            'messages' => $messages,
            'isDead' => $isDead,
            'target' => $target,
            'attackType' => $type
        ];
    }

    /**
     * @ParamConverter("mapObject", class="Dba\GameBundle\Entity\MapObject")
     * @Annotations\Post("/pickup/{mapObject}")
     */
    public function postPickupAction(MapObject $mapObject)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $mapObject, Player::PICKUP_ACTION)) {
            // failed to pickup
            return $this->forbidden();
        }

        $objectService = $this->services()->getObjectService();
        $messages = [];
        switch ($mapObject->getMapObjectType()->getId()) {
            case MapObjectType::ZENI:
                $player->setZeni($player->getZeni() + $mapObject->getNumber());
                $messages[] = $this->trans('action.pickup.zeni', ['%zeni%' => $mapObject->getNumber()]);
                break;

            case MapObjectType::BUSH:
                $objectService->cloneMapObject($mapObject);
                // No Break
            case MapObjectType::BOX:
                $playerObject = $objectService->addToInventory(
                    $player,
                    $mapObject->getObject(),
                    false,
                    $mapObject->getNumber()
                );
                if (is_numeric($playerObject)) {
                    $this->addFlash(
                        'danger',
                        $this->trans('action.pickup.error.' . $result)
                    );
                    return $this->redirect($this->generateUrl('map', ['what' => 'elements']));
                } else {
                    $this->em()->persist($playerObject);
                    $messages[] = $this->trans(
                        'action.pickup.object',
                        [
                            '%number%' => $mapObject->getNumber(),
                            '%objectName%' => $this->trans($mapObject->getObject()->getName() . '.name', [], 'objects')
                        ]
                    );
                }
                break;

            case MapObjectType::CAPSULE_BLUE:
            case MapObjectType::CAPSULE_ORANGE:
            case MapObjectType::CAPSULE_GREEN:
            case MapObjectType::CAPSULE_BLACK:
            case MapObjectType::CAPSULE_RED:
                $objectService->cloneMapObject($mapObject);
                $result = $objectService->openCapsule($mapObject);
                if (!empty($result['damages'])) {
                    $messages[] = $this->trans(
                        'action.pickup.damage',
                        ['%damages%' => $result['damages']]
                    );
                    $playerService = $this->services()->getPlayerService();
                    if ($playerService->takeDamage($player, $result['damages'])) {
                        $messages[] = $this->trans(
                            'game.dead',
                            [
                                '%x%' => $player->getX(),
                                '%y%' => $player->getY(),
                            ]
                        );
                    }
                }

                if (!empty($result['object'])) {
                    $playerObject = $objectService->addToInventory(
                        $player,
                        $result['object']['entity'],
                        false,
                        $result['object']['quantity']
                    );

                    if (is_numeric($playerObject)) {
                        $this->addFlash(
                            'danger',
                            $this->trans('action.pickup.error.' . $result)
                        );
                        return $this->redirect($this->generateUrl('map', ['what' => 'elements']));
                    } else {
                        $this->em()->persist($playerObject);
                        $messages[] = $this->trans(
                            'action.pickup.capsule.object',
                            [
                                '%number%' => $result['object']['quantity'],
                                '%objectName%' => $this->trans(
                                    $result['object']['entity']->getName() . '.name',
                                    [],
                                    'objects'
                                )
                            ]
                        );
                    }
                }
                break;
        }

        $player->usePoints(Player::ACTION_POINT, Player::PICKUP_ACTION);

        $this->em()->persist($player);
        $this->em()->remove($mapObject);
        $this->em()->flush();

        return [
            'messages' => $messages,
        ];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @Annotations\Post("/steal/{target}")
     */
    public function postStealAction(Player $target)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $target, Player::STEAL_ACTION) ||
            $player->getId() == $target->getId() ||
            !$this->canMakeAction($player, $target)
        ) {
            // failed to steal
            return $this->forbidden();
        }

        $playerService = $this->services()->getPlayerService();
        if ($return = $playerService->protectLowLevel($player, $target)) {
            $this->em()->persist($player);
            $this->em()->flush();

            return [
                'messages' => $return,
                'target' => $target,
            ];
        }

        $messages = [];
        $this->dispatchEvent(DbaEvents::BEFORE_STEAL, $player, $target, ['messages' => &$messages]);
        list($luck, $zenisAdded) = $playerService->steal($player, $target);
        if ($luck >= -3 && $luck < -2.5) {
            $messages[] = $this->trans('action.steal.completelyMissed');
            $eventMessage = 'event.action.steal.completelyMissed';
        } elseif ($luck >= -2.5 && $luck < -1.5) {
            $messages[] = $this->trans('action.steal.missed');
            $eventMessage = 'event.action.steal.missed';
        } elseif ($luck >= -1.5 && $luck < -0.5) {
            $messages[] = $this->trans('action.steal.moderatelySuccessful');
            $eventMessage = 'event.action.steal.moderatelySuccessful';
        } elseif ($luck >= -0.5 && $luck < 0.5) {
            $messages[] = $this->trans('action.steal.successful');
            $eventMessage = 'event.action.steal.successful';
        } elseif ($luck >= 0.5 && $luck < 1.5) {
            $messages[] = $this->trans('action.steal.wellSuccessful');
            $eventMessage = 'event.action.steal.wellSuccessful';
        } elseif ($luck >= 1.5 && $luck < 2.5) {
            $messages[] = $this->trans('action.steal.veryWell');
            $eventMessage = 'event.action.steal.veryWell';
        } else {
            $messages[] = $this->trans('action.steal.perfectly');
            $eventMessage = 'event.action.steal.perfectly';
        }

        $messages[] = $this->trans('action.steal.zeni.added', ['%zenisAdded%' => $zenisAdded]);
        $playerService->addEvent(
            $player,
            $target,
            $eventMessage,
            [
                'zenisLost' => $zenisAdded
            ]
        );

        $player->usePoints(Player::ACTION_POINT, Player::STEAL_ACTION);
        $playerService->addBattlePoints($player, Player::STEAL_ACTION, $target, $messages);
        $this->dispatchEvent(
            DbaEvents::AFTER_STEAL,
            $player,
            $target,
            [
                'messages' => &$messages,
                'zenisAdded' => $zenisAdded,
            ]
        );

        $this->em()->persist($player);
        $this->em()->persist($target);
        $this->em()->flush();

        return [
            'messages' => $messages,
            'target' => $target,
        ];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @Annotations\Post("/analysis/{target}")
     */
    public function postAnalysisAction(Player $target)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $target, Player::ANALYSIS_ACTION) ||
            $player->getId() == $target->getId()
        ) {
            // failed to analysis
            return $this->forbidden();
        }

        $playerService = $this->services()->getPlayerService();
        $messages = [];
        $this->dispatchEvent(DbaEvents::BEFORE_ANALYSIS, $player, $target, ['messages' => &$messages]);
        list($luck, $competences) = $playerService->analysis($player, $target);

        if ($luck >= 56 and $luck <= 61) {
            $messages[] = $this->trans('action.analysis.catastrophic'); //'Évaluation catastrophique...';
        } elseif ($luck >= 45 and $luck <= 55) {
            $messages[] = $this->trans('action.analysis.really.failed'); //'Évaluation vraiment échouée...';
        } elseif ($luck >= 30 and $luck <= 44) {
            $messages[] = $this->trans('action.analysis.failed'); //'Évaluation échouée';
        } elseif ($luck >= 15 and $luck <= 29) {
            $messages[] = $this->trans('action.analysis.moderatelySuccessful'); //'Évaluation moyennement réussie';
        } elseif ($luck >= 8 and $luck <= 14) {
            $messages[] = $this->trans('action.analysis.successful'); //'Évaluation bien réussie !';
        } elseif ($luck >= 1 and $luck <= 7) {
            $messages[] = $this->trans('action.analysis.wellSuccessful'); //'Évaluation très bien réussie !';
        } elseif ($luck == 0) {
            $messages[] = $this->trans('action.analysis.excellent'); //'Évaluation excellente !!';
        }

        $player->usePoints(Player::ACTION_POINT, Player::ANALYSIS_ACTION);
        $playerService->addBattlePoints($player, Player::ANALYSIS_ACTION, $target, $messages);
        $this->dispatchEvent(
            DbaEvents::AFTER_ANALYSIS,
            $player,
            $target,
            [
                'messages' => &$messages,
                'competences' => $competences,
            ]
        );

        $this->em()->persist($player);
        $this->em()->persist($target);
        $this->em()->flush();

        return [
            'messages' => $messages,
            'competences' => $competences,
        ];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @Annotations\Post("/slap/{target}")
     */
    public function postSlapAction(Player $target)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $target, Player::SLAP_ACTION) ||
            $target->getBetrayals() <= 0 ||
            $player->getId() == $target->getId()
        ) {
            // failed to slap
            return $this->forbidden();
        }

        $playerService = $this->services()->getPlayerService();
        $damages = 1;
        $messages = [];
        $this->dispatchEvent(DbaEvents::BEFORE_SLAP, $player, $target, ['messages' => &$messages]);
        if ($isDead = $playerService->takeDamage($target, $damages, true)) {
            $this->checkHeadPrice($player, $target, $messages);
            $player->setBattlePoints($player->getBattlePoints() + self::DEFAULT_BATTLE_POINT_KILL_SLAP);
            $playerService->addAttackStats($player, $target, $damages, true, Player::ATTACK_TYPE_SLAP);
            $eventMessage = 'event.action.slap.killed';
            $messages[] = $this->trans('action.slap.killed');
            $messages[] = $this->trans('action.slap.battlePoints');
        } else {
            $eventMessage = 'event.action.slap.damages';
            $messages[] = $this->trans('action.slap.damages');
        }

        $player->setNbSlapGiven($player->getNbSlapGiven() + 1);
        $playerService->addBattlePoints($player, Player::SLAP_ACTION, $target, $messages);
        $playerService->addEvent(
            $player,
            $target,
            $eventMessage
        );

        $this->dispatchEvent(
            DbaEvents::AFTER_SLAP,
            $player,
            $target,
            [
                'messages' => &$messages,
                'isDead' => $isDead,
            ]
        );

        $this->em()->persist($player);
        $this->em()->persist($target);
        $this->em()->flush();

        return [
            'messages' => $messages,
            'target' => $target,
            'isDead' => $isDead,
        ];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @Annotations\Get("/give/{target}")
     */
    public function getGiveAction(Player $target)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $target, Player::GIVE_ACTION) ||
            $player->getId() == $target->getId()
        ) {
            // failed to give
            return $this->forbidden();
        }

        $objects = [];
        $playerObjects = $player->getPlayerObjects();
        foreach ($playerObjects as $playerObject) {
            if (empty($playerObject->getNumber()) || !$playerObject->getObject()->isEnabled()) {
                continue;
            }

            $objects[] = $playerObject;
        }

        return [
            'target' => $target,
            'playerObjects' => $objects,
        ];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object", isOptional="true")
     * @Annotations\Post("/give/{target}", name="_target")
     * @Annotations\Post("/give/{target}/{object}", name="_object")
     */
    public function postGiveAction(Request $request, Player $target, Object $object = null)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $target, Player::GIVE_ACTION) ||
            $player->getId() == $target->getId()
        ) {
            // failed to give
            return $this->forbidden();
        }

        $messages = [];
        if (empty($object)) {
            $zenis = $request->request->get('zenis');
            if (!empty($zenis)) {
                $zenis = $zenis > $player->getZeni()  ? $player->getZeni() : $zenis;
                $player->setZeni($player->getZeni() - $zenis);
                $target->setZeni($target->getZeni() + $zenis);
                $player->usePoints(Player::ACTION_POINT, Player::GIVE_ACTION);
                $this->em()->persist($target);
                $this->em()->persist($player);
                $this->em()->flush();
                $this->services()->getPlayerService()->addEvent(
                    $player,
                    $target,
                    'event.action.give.zeni',
                    [
                        'zenis' => $zenis
                    ]
                );
            }
        } else {
            $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
                [
                    'player' => $player,
                    'object' => $object
                ]
            );

            if (empty($playerObject) or empty($playerObject->getNumber())) {
                return $this->forbidden();
            }

            $quantity = $request->request->get('quantity', 1);
            if ($quantity > $playerObject->getNumber()) {
                $quantity = $playerObject->getNumber();
            }

            $objectService = $this->services()->getObjectService();
            $targetObject = $objectService->addToInventory(
                $target,
                $playerObject->getObject(),
                false,
                $quantity
            );

            if (is_numeric($targetObject)) {
                $messages[] = $this->trans('action.give.error.' . $targetObject);
            } else {
                $playerObject->setNumber($playerObject->getNumber() - $quantity);
                $player->usePoints(Player::ACTION_POINT, Player::GIVE_ACTION);
                $this->em()->persist($targetObject);
                $this->em()->persist($playerObject);
                $this->em()->persist($target);
                $this->em()->persist($player);
                $this->em()->flush();
                $this->em()->refresh($playerObject);
                $this->services()->getPlayerService()->addEvent(
                    $player,
                    $target,
                    'event.action.give.item',
                    [
                        'objectName' => $this->trans(
                            $playerObject->getObject()->getName() . '.name',
                            [],
                            'objects'
                        ),
                        'quantity' => $quantity
                    ]
                );
            }
        }

        $objects = [];
        $playerObjects = $player->getPlayerObjects();
        foreach ($playerObjects as $playerObject) {
            if (empty($playerObject->getNumber()) || !$playerObject->getObject()->isEnabled()) {
                continue;
            }

            $objects[] = $playerObject;
        }

        return [
            'messages' => $messages,
            'target' => $target,
            'playerObjects' => $objects,
        ];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @Annotations\Post("/heal/{target}")
     */
    public function postHealAction(Player $target)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $target, Player::HEAL_ACTION) ||
            $target->getHealth() == $target->getTotalMaxHealth()
        ) {
            // failed to heal
            return $this->forbidden();
        }

        $playerService = $this->services()->getPlayerService();
        $messages = [];
        $this->dispatchEvent(DbaEvents::BEFORE_HEAL, $player, $target, ['messages' => &$messages]);
        list($luck, $healPoints, $fatiguePoints) = $playerService->heal($player, $target);

        if ($luck < -20) {
            $messages[] = $this->trans('action.heal.completelyMissed');
            $eventMessage = 'event.action.heal.completelyMissed';
        } elseif ($luck >= -20 && $luck < -10) {
            $messages[] = $this->trans('action.heal.missed');
            $eventMessage = 'event.action.heal.missed';
        } elseif ($luck >= -10 && $luck < -5) {
            $messages[] = $this->trans('action.heal.moderatelySuccessful');
            $eventMessage = 'event.action.heal.moderatelySuccessful';
        } elseif ($luck >= -5 && $luck < 0) {
            $messages[] = $this->trans('action.heal.successful');
            $eventMessage = 'event.action.heal.successful';
        } elseif ($luck >= 0 && $luck < 5) {
            $messages[] = $this->trans('action.heal.wellSuccessful');
            $eventMessage = 'event.action.heal.wellSuccessful';
        } elseif ($luck >= 5 && $luck < 15) {
            $messages[] = $this->trans('action.heal.veryWell');
            $eventMessage = 'event.action.heal.veryWell';
        } else {
            $messages[] = $this->trans('action.heal.perfectly');
            $eventMessage = 'event.action.heal.perfectly';
        }

        $messages[] = $this->trans('action.heal.restore', ['%healPoints%' => $healPoints]);
        $playerService->addEvent(
            $player,
            $target,
            $eventMessage,
            [
                'healPoints' => $healPoints
            ]
        );

        if (!empty($fatiguePoints)) {
            $eventMessage = 'event.action.heal.fatigue';
            if ($target->getFatiguePoints() > 0) {
                $messages[] = $this->trans('action.heal.fatigue.decrease');
            } else {
                $messages[] = $this->trans('action.heal.fatigue.disappear');
            }

            $playerService->addEvent(
                $player,
                $target,
                $eventMessage,
                [
                    'fatiguePoints' => $fatiguePoints,
                ]
            );
        }

        $playerService->addBattlePoints($player, Player::HEAL_ACTION, $target, $messages);
        $playerService->addHealStats($player, $healPoints);
        $player->usePoints(Player::ACTION_POINT, Player::HEAL_ACTION);
        $this->dispatchEvent(
            DbaEvents::AFTER_HEAL,
            $player,
            $target,
            [
                'messages' => &$messages,
                'fatiguePoints' => $fatiguePoints,
                'healPoints' => $healPoints,
            ]
        );

        $this->em()->persist($player);
        $this->em()->persist($target);
        $this->em()->flush();

        return [
            'messages' => $messages,
            'target' => $target,
            'canBeHealed' => $target->getHealth() < $target->getTotalMaxHealth(),
        ];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @Annotations\Get("/spell/{target}")
     */
    public function getSpellAction(Player $target)
    {
        $player = $this->getUser();
        if ($player->getActionPoints() < Player::SPELL_ACTION ||
            $player->getMap()->getId() != $target->getMap()->getId() ||
            $player->getId() == $target->getId() ||
            (!empty($playerSpell) && !$this->canMakeAction($player, $target, $playerSpell))
        ) {
            // failed to attack
            return $this->forbidden();
        }

        return [
            'playerSpells' => $player->getPlayerSpells(),
            'messages' => [],
            'target' => $target,
            'attackType' => null,
            'isDead' => false,
            'distances' => [
                'max_x' => abs($target->getX() - $player->getX()),
                'max_y' => abs($target->getY() - $player->getY())
            ]
        ];
    }

    /**
     * @ParamConverter("target", class="Dba\GameBundle\Entity\Player")
     * @ParamConverter("playerSpell", class="Dba\GameBundle\Entity\PlayerSpell")
     * @Annotations\Post("/spell/{target}/{type}/{playerSpell}")
     */
    public function postSpellAction(Player $target, $type, PlayerSpell $playerSpell)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $target, Player::SPELL_ACTION) ||
            $player->getId() == $target->getId() ||
            (!empty($playerSpell) && !$this->canMakeAction($player, $target, $playerSpell))
        ) {
            // failed to attack
            return $this->forbidden();
        }

        if (!$playerSpell->canBeUsed()) {
            return $this->forbidden();
        }

        if ($playerSpell->getSpell()->getType() == Spell::TYPE_ATTACK) {
            if ($type == Player::ATTACK_TYPE_REVENGE && $player->getTarget()->getId() != $target->getId()) {
                // Attack is just normal can't revenge
                $type = null;
            } elseif ($type == Player::ATTACK_TYPE_BETRAY &&
                      $player->getSide()->getId() != $target->getSide()->getId()
            ) {
                // Attack is just normal can't betray
                $type = null;
            } elseif (empty($type) && $player->getSide()->getId() == $target->getSide()->getId()) {
                $type = Player::ATTACK_TYPE_BETRAY;
            }
        }

        $playerService = $this->services()->getPlayerService();
        if ($return = $playerService->protectLowLevel($player, $target)) {
            $this->em()->persist($player);
            $this->em()->flush();

            return [
                'messages' => $return,
                'target' => $target,
                'attackType' => $type,
                'isDead' => false
            ];
        }

        $messages = [];
        $messages[] = $this->trans(
            'action.spell.cast',
            [
                '%name%' => $this->trans($playerSpell->getSpell()->getName() . '.name', [], 'spells')
            ]
        );

        $this->dispatchEvent(
            DbaEvents::BEFORE_SPELL,
            $player,
            $target,
            ['type' => $type, 'messages' => &$messages, 'playerSpell' => $playerSpell]
        );

        $damages = 0;
        $isDead = false;
        list($luck, $damages, $isDead) = $playerService->spell($player, $target, $playerSpell);

        if ($luck !== null) {
            if ($type == Player::ATTACK_TYPE_BETRAY) {
                $messages[] = $this->trans('action.betray');
            } elseif ($type == Player::ATTACK_TYPE_REVENGE) {
                $messages[] = $this->trans('action.revenge');
            }

            if (($luck >= -4 && $luck < -3) || $damages <= 0) {
                $messages[] = $this->trans('action.spell.dodge');
                $eventMessage = 'event.action.spell.dodge';
            } elseif ($luck >= -3 && $luck < -2.25) {
                $messages[] = $this->trans('action.spell.really.failed');
                $eventMessage = 'event.action.spell.really.failed';
            } elseif ($luck >= -2.25 && $luck < -1.5) {
                $messages[] = $this->trans('action.spell.failed');
                $eventMessage = 'event.action.spell.failed';
            } elseif ($luck >= -1.5 && $luck < 0.5) {
                $messages[] = $this->trans('action.spell.mediocre');
                $eventMessage = 'event.action.spell.moderately.succeeded';
            } elseif ($luck >= 0.5 && $luck < 1) {
                $messages[] = $this->trans('action.spell.successful');
                $eventMessage = 'event.action.spell.succeeded';
            } elseif ($luck >= 1 && $luck < 1.5) {
                $messages[] = $this->trans('action.spell.nice');
                $eventMessage = 'event.action.spell.nice';
            } elseif ($luck >= 1.5 && $luck < 5) {
                $messages[] = $this->trans('action.spell.critical');
                $eventMessage = 'event.action.spell.critic';
            } elseif ($luck >= 5 && $luck <= 6) {
                $messages[] = $this->trans('action.spell.master');
                $eventMessage = 'event.action.spell.master';
            }

            $messages[] = $this->trans('action.spell.damages', ['%damages%' => $damages]);
            if ($type == Player::ATTACK_TYPE_BETRAY) {
                $messages[] = $this->trans('action.betrayPoints');
            }
            $playerService->addEvent(
                $player,
                $target,
                $eventMessage,
                [
                    'damages' => $damages
                ]
            );
        }

        $playerService->addBattlePoints($player, Player::SPELL_ACTION, $target, $messages);

        if ($isDead) {
            $messages[] = $this->trans('action.killed');
            if (!$target->isPlayer()) {
                $target->setZeni($target->getZeni() + self::DEFAULT_NPC_ZENI);
            } else {
                $playerService->addEvent(
                    $player,
                    $target,
                    'event.action.killed'
                );
            }

            $this->checkHeadPrice($player, $target, $messages);
            $messages[] = $this->trans('action.spell.kill.battlePoints');
            $player->setBattlePoints($player->getBattlePoints() + self::DEFAULT_BATTLE_POINT_KILL);

            if ($type == Player::ATTACK_TYPE_REVENGE) {
                $player->setTarget(null);
                $messages[] = $this->trans('action.spell.kill.revenge');
            }
        }

        $playerService->addAttackStats($player, $target, $damages, $isDead, $type);
        $playerService->addSpellStats($player, $playerSpell);

        $player->usePoints(Player::ACTION_POINT, Player::SPELL_ACTION);
        $this->dispatchEvent(
            DbaEvents::AFTER_SPELL,
            $player,
            $target,
            [
                'type' => $type,
                'messages' => &$messages,
                'isDead' => $isDead,
                'damages' => $damages,
                'playerSpell' => $playerSpell,
            ]
        );

        $this->em()->persist($player);
        $this->em()->persist($target);
        $this->em()->persist($playerSpell);
        $this->em()->flush();

        return [
            'playerSpells' => $player->getPlayerSpells(),
            'messages' => $messages,
            'isDead' => $isDead,
            'target' => $target,
            'attackType' => $type,
            'distances' => [
                'max_x' => abs($target->getX() - $player->getX()),
                'max_y' => abs($target->getY() - $player->getY())
            ]
        ];
    }

    /**
     * Check for price on target's head
     *
     * @param Player $player Player
     * @param Player $target Target
     * @param array $messages Displayed messages
     */
    protected function checkHeadPrice(Player $player, Player $target, array &$messages = [])
    {
        if (!$target->getHeadPrice()) {
            return;
        }

        $messages[] = $this->trans(
            'action.head.price',
            [
                '%headPrice%' => $target->getHeadPrice()
            ]
        );

        $player->setZeni($player->getZeni() + $target->getHeadPrice());
        $target->setHeadPrice(0);
        $player->setNbWanted($player->getNbWanted() + 1);
    }

    /**
     * Can make action
     *
     * @param Player $player Player
     * @param Player $target Target
     * @param PlayerSpell $playerSpell Player spell
     *
     * @return boolean
     */
    protected function canMakeAction(Player $player, Player $target, PlayerSpell $playerSpell = null)
    {
        $mapRepo = $this->repos()->getMapRepository();
        if (!empty($playerSpell)) {
            if (abs($player->getX() - $target->getX()) > $playerSpell->getSpell()->getDistance() ||
                abs($player->getY() - $target->getY()) > $playerSpell->getSpell()->getDistance() ||
                $playerSpell->getPlayer()->getId() != $player->getId()
            ) {
                return false;
            }
        }

        return $mapRepo->hasValidPosition($player, MapBonus::TYPE_RESPAWN) ||
            $mapRepo->hasValidPosition($target, MapBonus::TYPE_RESPAWN);
    }

    /**
     * Dispatch event
     *
     * @param string $eventName Event name
     * @param Player $player Player
     * @param Player $target Target
     */
    protected function dispatchEvent($eventName, Player $player, Player $target = null, array $data = [])
    {
        $event = new ActionEvent();
        $event->setPlayer($player);
        if (!empty($target)) {
            $event->setTarget($target);
        }
        $event->setData($data);
        $this->get('event_dispatcher')->dispatch(
            $eventName,
            $event
        );
    }

    /**
     * Check player and target prosition
     *
     * @param Player  $player Player
     * @param mixed  $target Player or MapObject
     * @param integer $actionPoints Action points
     */
    protected function checkPosition(Player $player, $target, $actionPoints)
    {
        return $player->getActionPoints() < $actionPoints ||
            $player->getX() != $target->getX() ||
            $player->getY() != $target->getY() ||
            $player->getMap()->getId() != $target->getMap()->getId();
    }
}
