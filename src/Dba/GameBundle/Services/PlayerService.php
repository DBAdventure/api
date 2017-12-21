<?php

namespace Dba\GameBundle\Services;

use Dba\GameBundle\Entity\EventType;
use Dba\GameBundle\Entity\Map;
use Dba\GameBundle\Entity\MapObject;
use Dba\GameBundle\Entity\MapObjectType;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\PlayerEvent;
use Dba\GameBundle\Entity\PlayerSpell;
use Dba\GameBundle\Entity\PlayerSpellEffect;
use Dba\GameBundle\Entity\QuestNpc;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Entity\Spell;

class PlayerService extends BaseService
{
    const PILAF_ATTACK = 50;

    /**
     * Check for protect low level
     *
     * @return boolean|array
     */
    public function protectLowLevel(
        Player $player,
        Player $target
    ) {
        if ($target->isPlayer() &&
            $player->getLevel() >= 12 &&
            ($player->getLevel() * 0.5) > $target->getLevel()
        ) {
            $return = [
                'game.attack.protect',
                [
                    'message' => 'game.lose.life',
                    'parameters' => [
                        'damages' => self::PILAF_ATTACK
                    ]
                ]
            ];

            if ($this->takeDamage($player, self::PILAF_ATTACK)) {
                $return[] = 'game.you.die';
            }

            return $return;
        }

        return false;
    }

    /**
     *  Respawn a player
     *
     * @param Player $player Player
     *
     */
    public function respawn(Player $player)
    {
        if ($player->getMap()->isTutorial()) {
            // Don't care so continue
            return;
        }

        if (!$player->isPlayer()) {
            $where = ['c'] + Player::AVAILABLE_MOVE;
            return $this->teleport(
                $player,
                $player->getMap(),
                $where[array_rand($where)]
            );
        }

        $map = $this->repos()->getMapRepository()->findOneBy([
            'id' => $player->getSide()->getId() == Side::GOOD ? Map::HEAVEN : Map::HELL
        ]);
        $player->setX(mt_rand(1, $map->getMaxX()));
        $player->setY(mt_rand(1, $map->getMaxY()));
        $player->setMap($map);
    }

    /**
     *  Respawn a player
     *
     * @param Player $player Player
     *
     */
    public function forbiddenTeleport(Player $player, Map $map)
    {
        $partX = round($map->getMaxX() / 3);
        $partY = round($map->getMaxY() / 3);

        // Center of the map
        $xMin = $partX;
        $xMax = $partX * 2;
        $yMin = $partY;
        $yMax = $partY * 2;

        $forbiddenTeleport = '';
        if ($player->getY() >= $yMax) {
            $forbiddenTeleport .= 's';
        }

        if ($player->getY() <= $yMin) {
            $forbiddenTeleport .= 'n';
        }

        if ($player->getX() >= $xMax) {
            $forbiddenTeleport .= 'e';
        }

        if ($player->getX() <= $xMin) {
            $forbiddenTeleport .= 'w';
        }

        return $forbiddenTeleport;
    }

    /**
     * Attack action
     *
     * @param Player $player Player
     * @param Player $target Target
     *
     * @return mixed
     */
    public function attack(Player $player, Player $target)
    {
        // test player agility
        // if target more agile, 1/3 luck to do damage between 1 and 10
        $luck = rand(-12, 24) / 4;
        if ($player->getTotalAccuracy() > (0.90 * $target->getTotalAgility())) {
            $ratio = (225/7942) * (
                (1.10 * $player->getTotalStrength() + 0.3 * $player->getTotalAccuracy()) -
                (
                    2 * $target->getTotalResistance() +
                    0.4 * $target->getTotalAgility() +
                    0.6 * $target->getTotalAnalysis()
                )
            ) + (3375/7942);

            // target is too strong
            if ($ratio < 0) {
                $ratio = abs($ratio);
                $competences = 17 * sqrt($ratio);
                $fatigue = ($target->getFatiguePoints() - $player->getFatiguePoints()) * (8/100);
                $totalPercent = -($luck + $competences - $fatigue - 7);
                $damages = ceil((mt_rand(25, 30) * $totalPercent) / 7);
            } else {
                $competences = 19 * sqrt($ratio);
                $fatigue = ($target->getFatiguePoints() - $player->getFatiguePoints()) * (8/100);
                $totalPercent = ($luck + $competences + $fatigue - 7);
                $damages = ceil(mt_rand(25, 30) * $totalPercent);
            }

            $damages = ceil($damages / 5.5);
            if ($damages < 0) {
                $damages = mt_rand(1, 5);
            }
        } else {
            // He touched him
            $damages = mt_rand(1, 3);
            if (in_array($damages, [1, 2])) {
                $luck = -3;
            } else {
                // dodge
                $damages = 0;
                $luck = -4;
            }
        }

        $isDead = $this->takeDamage($target, $damages);
        return [$luck, $damages, $isDead];
    }

    /**
     * Spell action
     *
     * @param Player $player Player
     * @param Player $target Target
     * @param PlayerSpell $playerSpell Player spell
     *
     * @return mixed
     */
    public function spell(Player $player, Player $target, PlayerSpell $playerSpell)
    {
        $spellType = $playerSpell->getSpell()->getType();
        if ($spellType != Spell::TYPE_ATTACK) {
            $playerSpellEffect = new PlayerSpellEffect();
            $playerSpellEffect->setPlayerSpell($playerSpell);

            if ($spellType == Spell::TYPE_PLAYER) {
                $playerSpellEffect->setTarget($player);
            } else {
                $playerSpellEffect->setTarget($target);
            }

            if (!empty($playerSpell->getSpell()->getBonus()['times_used'])) {
                $playerSpellEffect->setTimesUsed($playerSpell->getSpell()->getBonus()['times_used']);
            }

            $playerSpell->addPlayerSpellEffect($playerSpellEffect);

            return [null, null, false];
        }

        $luck = rand(-6, 6) / 4;
        $ratio = (225/7942) * (
            (
                (1.4 * $player->getTotalIntellect()) +
                (0.4 * $player->getTotalVision()) +
                ((($player->getTotalMaxKi() - 1) / 2) * 0.6)
            ) - (
                (0.7 * $target->getTotalIntellect()) +
                (2 * $target->getTotalSkill()) +
                0.3 * $target->getTotalResistance() +
                0.9 * $target->getTotalAnalysis()
            )
        ) + (3375/7942);

        $baseDamages = $playerSpell->getSpell()->getDamages();
        $fatigue = ($target->getFatiguePoints() - $player->getFatiguePoints()) * (8/100);
        if ($ratio < 0) {
            $ratio = abs($ratio);
            $competences = 17 * sqrt($ratio);
            $totalPercent = -($luck + $competences - $fatigue - 7);
        } else {
            $competences = 19 * sqrt($ratio);
            $totalPercent = ($luck + $competences + $fatigue - 7);
        }
        $damages = mt_rand(ceil($baseDamages / 2), $baseDamages) + ceil(mt_rand(25, 30) * $totalPercent);
        $damages = ceil($damages / 5.6);

        $isDead = $this->takeDamage($target, $damages);
        return [$luck, $damages, $isDead];
    }

    /**
     * Generic method to substract damage to a player
     * If player isn't dead return false,
     * otherwise run respawn and forbiddenTeleport commands
     * and return true
     *
     * @param Player $player
     * @param integer $damages
     * @param boolean $isSlap
     *
     * @return boolean
     */
    public function takeDamage(Player $player, $damages, $isSlap = false)
    {
        $this->repos()->getMapRepository()->takeDamage(
            $player->getMap(),
            $player->getX(),
            $player->getY()
        );

        if (!$player->takeDamage($damages, true, $isSlap)) {
            return false;
        }

        $this->respawn($player);
        $this->forbiddenTeleport(
            $player,
            $this->repos()->getMapRepository()->getDefaultMap()
        );
        return true;
    }

    /**
     * Add battle points
     *
     * @param Player $player Player
     * @param integer $actionPointsUsed Action points used
     * @param Player $target Target
     * @param array $messages Array of Messages
     * @param integer $battlePoints BattlePoints
     */
    public function addBattlePoints(
        Player $player,
        $actionPointsUsed,
        Player $target = null,
        array &$messages = [],
        $battlePoints = null
    ) {
        if ($battlePoints === null && $target !== null) {
            $battlePoints = $this->calculateBattlePoints($player, $actionPointsUsed, $target);
        }

        if (!empty($battlePoints)) {
            $messages[] = [
                'message' => 'action.battle.points',
                'parameters' => [
                    'battlePoints' => $battlePoints
                ]
            ];
        }

        if ($this->updateLevel($player, $battlePoints)) {
            $messages[] = [
                'message' => 'action.level.up',
                'parameters' => [
                    'level' => $player->getLevel()
                ]
            ];
        }
    }

    /**
     * Calculate battle points
     *
     * @param Player $player Player
     * @param integer $actionPointsUsed Action points used
     * @param Player $target Target
     *
     * @return Player
     */
    protected function calculateBattlePoints(Player $player, $actionPointsUsed, Player $target)
    {
        if (!$player->isPlayer()) {
            return 0;
        }

        if ($actionPointsUsed == Player::SLAP_ACTION) {
            return 1;
        }

        $baseBp = 5;
        // Guy attack a NPC, reduce the base
        if (!$target->isPlayer()) {
            $baseBp -= 1;
        }

        // Attack a lower level, we penalize for every ten levels
        // No penality for healing
        // High level, give him a reward
        if (($actionPointsUsed != Player::HEAL_ACTION || $player->getLevel() > $target->getLevel()) ||
            ($player->getLevel() < $target->getLevel() && $target->isPlayer())
        ) {
            $baseBp -= round(($player->getLevel() - $target->getLevel()) / 10);
        }

        $battlePoints = $actionPointsUsed * $baseBp;
        return $battlePoints < 0 ? 1 : $battlePoints;
    }

    /**
     * Update level
     *
     * @param Player $player
     * @param integer $battlePoints
     *
     * @return boolean Return true if level is updated, otherwise false
     */
    protected function updateLevel(Player $player, $battlePoints = null)
    {
        if (!empty($battlePoints)) {
            $player->setBattlePoints($player->getBattlePoints() + $battlePoints);
        }

        if ($player->getBattlePoints() > $player->getBattlePointsRemaining()) {
            // Pallier par niveau
            $skillPoints = 5;
            $player->setLevel($player->getLevel() + 1);
            if ($player->getLevel() >= 10 && $player->getLevel() <= 19) {
                $skillPoints = 8;
            } elseif ($player->getLevel() > 19 && $player->getLevel() <= 29) {
                $skillPoints = 10;
            } elseif ($player->getLevel() > 29 && $player->getLevel() <= 39) {
                $skillPoints = 11;
            } elseif ($player->getLevel() > 39) {
                $skillPoints = 12;
            }

            $player->addPoints(Player::ACTION_POINT, 20);
            $player->addPoints(Player::KI_POINT, $player->getTotalMaxKi());
            $player->setSkillPoints($player->getSkillPoints() + $skillPoints);
            $player->setMaxHealth($player->getMaxHealth() + 10);

            return true;
        }

        return false;
    }

    /**
     * Teleport player somewhere, depends on map and position
     *
     * @param Player $player Player
     * @param Map $map Map
     * @param string $where String where (n,s,ne,nw,se,sw)
     */
    public function teleport(Player $player, Map $map, $where)
    {
        $partX = round($map->getMaxX() / 3);
        $partY = round($map->getMaxY() / 3);

        // Center of the map
        $xMin = $partX;
        $xMax = $partX * 2;
        $yMin = $partY;
        $yMax = $partY * 2;

        if (preg_match('~n~', $where)) {
            $yMin = 1;
            $yMax = $partY;
        }

        if (preg_match('~s~', $where)) {
            $yMin = $partY * 2;
            $yMax = $map->getMaxY();
        }

        if (preg_match('~e~', $where)) {
            $xMin = $partX * 2;
            $xMax = $map->getMaxX();
        }

        if (preg_match('~w~', $where)) {
            $xMin = 1;
            $xMax = $partX;
        }

        $position = $this->repos()->getMapRepository()->findPosition(
            $map,
            $xMin,
            $xMax,
            $yMin,
            $yMax
        );
        $player->setX($position['x']);
        $player->setY($position['y']);
        $player->setMap($map);
        $player->setForbiddenTeleport(null);
    }

    /**
     * Steal action
     *
     * @param Player $player Player
     * @param Player $target Target
     *
     * @return mixed
     */
    public function steal(Player $player, Player $target)
    {
        $fatigue = ($target->getFatiguePoints() - $player->getFatiguePoints()) * (8/100);
        $luck = rand(-12, 12) / 4;
        $ratio = (225/7942) * (
            (
                (2 * $player->getTotalAgility()) + (0.5 * $player->getTotalAccuracy())
            ) - (
                (1.8 * $target->getTotalAgility()) +
                (0.7 * $target->getTotalStrength()) +
                ($target->getTotalAnalysis() * 0.8)
            )
        ) + (3375/7942);

        if ($ratio < 0) {
            $ratio = abs($ratio);
            $totalPercent = - ($luck + (17 * sqrt($ratio)) - $fatigue);
            $zenisAdded = ceil(abs(ceil($totalPercent)/7)) + mt_rand(0, 5);
        } else {
            $totalPercent = ($luck + (19 * sqrt($ratio)) + $fatigue);
            $zenisAdded = abs(ceil($totalPercent)) + mt_rand(0, 10);
        }

        $zenisAdded *= 0.70;
        $zenisAdded = ceil($zenisAdded);

        if ($zenisAdded > $target->getZeni()) {
            $zenisAdded = $target->getZeni();
        }

        $target->setZeni($target->getZeni() - $zenisAdded);
        $player->setZeni($player->getZeni() + $zenisAdded);
        $player->setNbStolenZeni($player->getNbStolenZeni() + $zenisAdded);
        $player->setNbActionStolenZeni($player->getNbActionStolenZeni() + 1);

        return [
            $luck,
            $zenisAdded
        ];
    }

    /**
     * Heal action
     *
     * @param Player $player Player
     * @param Player $target Target
     *
     * @return mixed
     */
    public function heal(Player $player, Player $target)
    {
        if ($target->getHealth() == $target->getTotalMaxHealth()) {
            // @TODO Heal: Use constant
            return 'FULL_HEALTH';
        }

        $luck = 25 * rand(-5, 5) / 5;
        if ($player->getFatiguePoints() <= 50) {
            $playerLuckFactor = -2 / 125 * $player->getFatiguePoints() + 1;
        } else {
            $playerLuckFactor = 0.2;
        }

        if ($target->getFatiguePoints() <= 30) {
            $targetLuckFactor = 0.01 * $target->getFatiguePoints() + 1;
        } else {
            $targetLuckFactor = 1.3;
        }

        $competence = 2.75 * ( 0.7 * $player->getTotalSkill() + 0.1 * $player->getTotalIntellect());
        $healPoints = ceil(($playerLuckFactor * $targetLuckFactor) * ($luck + $competence));
        $healPoints = $healPoints < 0 ? 0 : $healPoints;
        $fatiguePoints = 0;
        if ($player->getTotalSkill() >= 15) {
            if ($player->getTotalSkill() >= 15 && $player->getTotalSkill() <= 25) {
                $fatiguePoints = mt_rand(1, 5);
            } elseif ($player->getTotalSkill() >= 26 and $player->getTotalSkill() <= 40) {
                $fatiguePoints = mt_rand(3, 10);
            } elseif ($player->getTotalSkill() >= 41 and $player->getTotalSkill() <= 60) {
                $fatiguePoints = mt_rand(7, 15);
            } elseif ($player->getTotalSkill() >= 61 and $player->getTotalSkill() <= 85) {
                $fatiguePoints = mt_rand(12, 20);
            } elseif ($player->getTotalSkill() >= 86 and $player->getTotalSkill() <= 115) {
                $fatiguePoints = mt_rand(15, 25);
            } elseif ($player->getTotalSkill() >= 116 and $player->getTotalSkill() <= 140) {
                $fatiguePoints = mt_rand(19, 30);
            } elseif ($player->getTotalSkill() > 140) {
                $fatiguePoints = mt_rand(31, 40);
            }
        }

        $target->addPoints(Player::HEALTH_POINT, $healPoints);
        $target->usePoints(Player::FATIGUE_POINT, $fatiguePoints);


        return [$luck, $healPoints, $fatiguePoints];
    }

    /**
     * Analysis action
     *
     * @param Player $player Player
     * @param Player $target Target
     *
     * @return mixed
     */
    public function analysis(Player $player, Player $target)
    {
        // Max competences number
        $nbMaxCompetences = 10;
        $base = 0;
        $skill = ceil(
            (
                0.8 * $player->getTotalAnalysis() +
                0.2 * $player->getTotalVision()
            ) - (
                0.6 * $target->getTotalAnalysis() + 0.4 * $target->getTotalVision()
            )
        );

        if ($skill < -5) {
            $base = 60;
        } elseif ($skill >= -5 && $skill < 0) {
            $base = 55;
        } elseif ($skill >= 0 && $skill < 3) {
            $base = 50;
        } elseif ($skill >= 3 && $skill < 6) {
            $base = 45;
        } elseif ($skill >= 6 && $skill < 9) {
            $base = 40;
        } elseif ($skill >= 9 && $skill < 11) {
            $base = 36;
        } elseif ($skill >= 11 && $skill < 13) {
            $base = 31;
        } elseif ($skill >= 13 && $skill < 15) {
            $base = 26;
        } elseif ($skill >= 15 && $skill < 18) {
            $base = 22;
        } elseif ($skill >= 18 && $skill < 22) {
            $base = 16;
        } elseif ($skill >= 22 && $skill < 26) {
            $base = 10;
        } elseif ($skill >= 26 && $skill < 30) {
            $base = 5;
        } elseif ($skill >= 30) {
            $base = 0;
        }

        $luck = $base - mt_rand(0, 20);
        if ($luck < 0) {
            $luck = 0;
        }

        if ($base == 60) {
            $luck = 61;
        } elseif ($base == 0) {
            $luck = 0;
        }

        // Caculate accuracy for analysis
        // Accuracy of evaluated competences (if strength = 20 and accuracy = 4,
        // we will displayed either 16 or 24
        $pre = round(($luck * 2.5) / 10);
        if ($pre == 0) {
            $pre = mt_rand(1, 10);
        }

        $nbCompetences = ($skill + 40) / $nbMaxCompetences;
        $nbCompetences = round($nbCompetences);
        if ($nbCompetences > $nbMaxCompetences) {
            $nbCompetences = $nbMaxCompetences;
        } elseif ($nbCompetences<1) {
            $nbCompetences = 1;
        }

        $competences = [
            'analysis' => $target->getTotalAnalysis(),
            'resistance' => $target->getTotalResistance(),
            'vision' => $target->getTotalVision(),
            'agility' => $target->getTotalAgility(),
            'strength' => $target->getTotalStrength(),
            'intellect' => $target->getTotalIntellect(),
            'accuracy' => $target->getTotalAccuracy(),
            'skill' => $target->getTotalSkill(),
            'zeni' => $target->getZeni(),
            'fatiguePoints' => $target->getFatiguePoints()
        ];

        $arrayDifference = [-1, 1];
        $randomCompetences = array_rand($competences, $nbCompetences);
        $resultCompetences = [];
        foreach ($randomCompetences as $competenceName) {
            $difference = $arrayDifference[mt_rand(0, 1)] * $pre;
            $competenceValue = $competences[$competenceName] + $difference;
            if ($competenceValue < 0) {
                $competenceValue = 1;
            }

            // We need to manage Zenis, if we do a bad analysis, we need a weird number
            if ($luck >= 56 and $luck <= 61) {
                $competenceValue += mt_rand(30, 70);
                if ($competenceName == 'ze') {
                    $competenceValue += mt_rand(3000, 6000);
                }
            } elseif ($luck >= 45 and $luck <= 55) {
                $competenceValue += mt_rand(15, 29);
                if ($competenceName == 'ze') {
                    $competenceValue += mt_rand(1000, 3000);
                }
            } elseif ($luck >= 30 and $luck <= 44) {
                $competenceValue += mt_rand(10, 14);
                if ($competenceName == 'ze') {
                    $competenceValue += mt_rand(0, 1500);
                }
            } elseif ($luck >= 15 and $luck <= 29) {
                $competenceValue += mt_rand(0, 9);
            }

            $resultCompetences[$competenceName] = (int) $competenceValue;
        }

        if ($target->isPlayer()) {
            if ($player->getTotalAnalysis() >= 20) {
                if ($luck >= 44) {
                    $resultCompetences['class'] = $target->getClass(true);
                } else {
                    $resultCompetences['class'] = $target->getClass();
                }
            } else {
                $resultCompetences['class'] = '???';
            }

            if ($player->getTotalAnalysis() >= 30) {
                if ($luck >= 44) {
                    $resultCompetences['movementPoints'] = mt_rand(0, 10);
                } else {
                    $resultCompetences['movementPoints'] = $target->getMovementPoints();
                }
            } else {
                $resultCompetences['movementPoints'] = '???';
            }

            if ($player->getTotalAnalysis() >= 40) {
                if ($luck >= 44) {
                    $resultCompetences['actionPoints'] = mt_rand(0, 5);
                } else {
                    $resultCompetences['actionPoints'] = $target->getActionPoints();
                }
            } else {
                $resultCompetences['actionPoints'] = '???';
            }

            if ($player->getTotalAnalysis() >= 60 or $player->getTotalVision() >= 70) {
                $resultCompetences['health'] = $target->getHealth();
                $temoinAmu = 1;
            } else {
                $resultCompetences['health'] = " ??? ";
                $temoinAmu = 2;
            }

            if ($player->getTotalAnalysis() >= 70 or $player->getTotalVision() >= 80) {
                $resultCompetences['ki'] = $target->getKi();
                $temoinBouclier = 1;
            } else {
                $resultCompetences['ki'] = " ??? ";
                $temoinBouclier = 2;
            }
        }

        // @TODO Analysis: display equipements

        return [$luck, $resultCompetences];
    }

    /**
     * Add Player event
     *
     * @param Player $player Player who cause the event
     * @param Player $target Player who receive the event
     * @param string $message Message to send to the target
     * @param array $parameters Message parameters
     */
    public function addEvent(
        Player $player,
        Player $target,
        $message,
        array $parameters = [],
        $eventType = EventType::PLAYER
    ) {
        if ($player->getId() == $target->getId() || $target->getSide()->getId() == Side::NPC) {
            // No need to save it
            return;
        }

        $event = new PlayerEvent();
        if (!empty($player->getId())) {
            $event->setPlayer($player);
        }

        $event->setTarget($target);
        $event->setMessage($message);
        $event->setParameters($parameters);
        $event->setEventType($this->repos()->getEventTypeRepository()->findOneById($eventType));
        $this->em()->persist($event);
        $this->em()->flush();
    }

    /**
     * Add stats when player cast spell
     *
     * @param Player $player Player Who cast spell
     *
     * @return null
     */
    public function addSpellStats(Player $player)
    {
        $player->setNbSpell($player->getNbSpell() + 1);
    }

    /**
     * Add stats when player attack target
     *
     * @param Player $player Player who attack
     * @param Player $target Player who receive attack
     * @param integer $damages Damage taken by target
     * @param boolean $isDead Check if target is dead
     * @param integer $attackType Attack type (revenge, normal, guild, etc)
     *
     * @return null
     */
    public function addAttackStats(Player $player, Player $target, $damages, $isDead = false, $attackType = null)
    {
        if ($attackType == Player::ATTACK_TYPE_BETRAY && $target->isPlayer()) {
            $player->setBetrayals($player->getBetrayals() + 1);
            $target->setTarget($player);
        } elseif (!$target->isPlayer()) {
            $target->setTarget($player);
        }

        switch ($target->getSide()->getId()) {
            case Side::GOOD:
                $player->setNbHitGood($player->getNbHitGood() + 1);
                $player->setNbDamageGood($player->getNbDamageGood() + $damages);
                if ($isDead) {
                    if (empty($attackType)) {
                        $player->setSidePoints($player->getSidePoints() + 1);
                    } elseif ($attackType == Player::ATTACK_TYPE_BETRAY && $target->isPlayer()) {
                        $player->setSidePoints(0);
                        $side = $this->repos()->getSideRepository()->findOneById(Side::BAD);
                        $player->setSide($side);
                    }
                }
                break;
            case Side::BAD:
                $player->setNbHitBad($player->getNbHitBad() + 1);
                $player->setNbDamageBad($player->getNbDamageBad() + $damages);
                if ($isDead) {
                    if (empty($attackType)) {
                        $player->setSidePoints($player->getSidePoints() - 1);
                    } elseif ($attackType == Player::ATTACK_TYPE_BETRAY && $target->isPlayer()) {
                        $player->setSidePoints(0);
                        $side = $this->repos()->getSideRepository()->findOneById(Side::GOOD);
                        $player->setSide($side);
                    }
                }
                break;
            case Side::NPC:
                $player->setNbHitNpc($player->getNbHitNpc() + 1);
                $player->setNbDamageNpc($player->getNbDamageNpc() + $damages);
                break;
        }
    }

    /**
     * Add stats when player heal target
     *
     * @param Player $player Player who heal
     * @param integer $healPoints Heal restored
     *
     * @return null
     */
    public function addHealStats(Player $player, $healPoints)
    {
        $player->setNbHealthGiven($player->getNbHealthGiven() + 1);
        $player->setNbTotalHealthGiven($player->getNbTotalHealthGiven() + $healPoints);
    }


    /**
     * Move player to a specific direction
     *
     * @param Player $player Player who will move
     * @param string $where Direction
     *
     * @return boolean
     */
    public function move(Player $player, $where)
    {
        $move = 0;
        if (preg_match('~n~', $where)) {
            $player->setY($player->getY() - 1);
            $move++;
        }

        if (preg_match('~s~', $where)) {
            $player->setY($player->getY() + 1);
            $move++;
        }

        if (preg_match('~e~', $where)) {
            $player->setX($player->getX() + 1);
            $move++;
        }

        if (preg_match('~w~', $where)) {
            $player->setX($player->getX() - 1);
            $move++;
        }

        return [$this->repos()->getMapRepository()->hasValidPosition($player), $move];
    }


    /**
     * Find available player objects
     *
     * @return array
     */
    public function getAvailableObjects(Player $player)
    {
        $objects = [];
        $playerObjects = $player->getPlayerObjects();
        foreach ($playerObjects as $playerObject) {
            if (empty($playerObject->getNumber()) || !$playerObject->getObject()->isEnabled()) {
                continue;
            }

            $objects[$playerObject->getObject()->getType()][] = $playerObject;
        }

        return $objects;
    }

    /**
     * Prepare tutorial
     * - Create Map
     * - Create Map Object
     *
     * @param Player $player The player
     * @return void
     */
    public function prepareTutorial(Player $player)
    {
        $originalTutorial = $this->repos()->getMapRepository()->findOneById(Map::TUTORIAL);
        if ($player->getMap()->isTutorial()) {
            $tutorial = $player->getMap();
        } else {
            $tutorial = clone $originalTutorial;
            $tutorial->setType(Map::TYPE_TUTORIAL);
            $mapBoxes = $this->repos()->getMapBoxRepository()->findBy(
                [
                    'map' => $originalTutorial
                ]
            );

            foreach ($mapBoxes as $mapBox) {
                $newMapBox = clone $mapBox;
                $newMapBox->setMap($tutorial);
                $this->em()->persist($newMapBox);
            }

            $mapObjects = $this->repos()->getMapObjectRepository()->findBy(
                [
                    'map' => $originalTutorial
                ]
            );

            foreach ($mapObjects as $mapObject) {
                $newMapObject = clone $mapObject;
                $newMapObject->setMap($tutorial);
                $this->em()->persist($newMapObject);
            }

            $quests = $this->repos()->getQuestRepository()->findBy(
                [
                    'map' => $originalTutorial
                ]
            );
            foreach ($quests as $quest) {
                $newQuest = clone $quest;
                $newQuest->setMap($tutorial);
                $this->em()->persist($newQuest);
            }

            $npcs = $this->repos()->getPlayerRepository()->findBy(
                [
                    'map' => $originalTutorial,
                    'side' => $this->repos()->getSideRepository()->findOneById(Side::NPC),
                ]
            );
            foreach ($npcs as $npc) {
                $newNpc = clone $npc;
                $newNpc->setMap($tutorial);
                $newNpc->setName($newNpc->getName() . mt_rand());
                $newNpc->setUsername($newNpc->getUsername() . mt_rand());
                $newNpc->setEmail($newNpc->getEmail() . mt_rand());
                $this->em()->persist($newNpc);
            }

            $this->em()->persist($tutorial);
            $player->setMap($tutorial);
            $player->setX(3);
            $player->setY(10);
        }

        $this->em()->flush();
    }
}
