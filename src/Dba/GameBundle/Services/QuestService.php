<?php

namespace Dba\GameBundle\Services;

use Dba\GameBundle\Entity\Map;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\PlayerQuest;
use Dba\GameBundle\Entity\Quest;
use Dba\GameBundle\Entity\Side;

class QuestService extends BaseService
{
    /**
     * Run quest event
     *
     * @param Player $player Player who cause the event
     * @param array $event Quest Event
     * @param array $messages Array of Messages
     */
    public function runEvent(Player $player, array $event, array &$messages = [])
    {
        if (!empty($event['message'])) {
            $messages[] = $event['message'];
        }

        if (!empty($event['x'])) {
            $player->setX($event['x']);
        }

        if (!empty($event['y'])) {
            $player->setY($event['y']);
        }

        if (!empty($event['map'])) {
            $map = $this->repos()->getMapRepository()->findOneById($event['map']);
            if ($map->getId() !== Map::TUTORIAL && !$map->isTutorial()) {
                $player->setMap($map);
                if (empty($event['y']) && empty($event['x'])) {
                    $this->services()->getPlayerService()->respawn($player, true);
                }
            }
        }
    }

    /**
     * Check if quest can be Done
     *
     * @param PlayerQuest $playerQuest Player Quest
     * @param array $messages Array of Messages
     *
     * @return boolean
     */
    public function canBeDone(PlayerQuest $playerQuest, array &$messages = [])
    {
        $quest = $playerQuest->getQuest();
        $canBeDone = true;

        $npcs = $playerQuest->getNpcs();
        foreach ($quest->getNpcsNeeded() as $obj) {
            if (!empty($npcs[$obj->getRace()->getId()]) && $npcs[$obj->getRace()->getId()] < $obj->getNumber()) {
                $canBeDone = false;
                break;
            }
        }

        $npcObjects = $playerQuest->getNpcObjects();
        foreach ($quest->getNpcObjectsNeeded() as $obj) {
            if (!empty($npcObjects[$obj->getNpcObject()->getId()]) &&
                $npcObjects[$obj->getNpcObject()->getId()] < $obj->getNumber()
            ) {
                $canBeDone = false;
                break;
            }
        }

        $playerObjectsNeeded = [];
        $playerObjectRepo = $this->repos()->getPlayerObjectRepository();
        foreach ($quest->getObjectsNeeded() as $obj) {
            $objectNeeded = $playerObjectRepo->findOneBy(
                [
                    'player' => $playerQuest->getPlayer(),
                    'object' => $obj->getObject(),
                ]
            );
            $playerObjectsNeeded[] = [
                'playerObject' => $objectNeeded,
                'number' => $obj->getNumber(),
            ];

            if ($objectNeeded->getNumber() < $obj->getNumber()) {
                $canBeDone = false;
                break;
            }
        }

        if ($canBeDone) {
            $this->runEvent($playerQuest->getPlayer(), $quest->getOnCompleted(), $messages);
        }

        return [$canBeDone, $playerObjectsNeeded];
    }
}
