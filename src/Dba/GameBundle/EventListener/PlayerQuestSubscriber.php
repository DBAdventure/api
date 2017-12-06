<?php

namespace Dba\GameBundle\EventListener;

use DateTime;
use Dba\GameBundle\Event\DbaEvents;
use Dba\GameBundle\Event\ActionEvent;
use Dba\GameBundle\Entity\Side;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PlayerQuestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            DbaEvents::AFTER_SPELL => 'afterAttack',
            DbaEvents::AFTER_ATTACK => 'afterAttack',
            DbaEvents::AFTER_SLAP => 'afterAttack',
        );
    }

    public function afterAttack(ActionEvent $event)
    {
        $messages = $event->getData()['messages'];
        $target = $event->getTarget();
        if (!empty($event->getData()['isDead']) && $target->getSide()->getId() == Side::NPC) {
            $player = $event->getPlayer();
            $playerQuests = $player->getPlayerQuests()->filter(
                function ($entity) {
                    return $entity->isInProgress();
                }
            );
            foreach ($player->getPlayerQuests() as $playerQuest) {
                $quest = $playerQuest->getQuest();

                // Check npc needed
                $npcs = $playerQuest->getNpcs();
                foreach ($quest->getNpcsNeeded() as $npcNeeded) {
                    // Current value for this race
                    $value = !empty($npcs[$npcNeeded->getRace()->getId()]) ? $npcs[$npcNeeded->getRace()->getId()] : 0;
                    // is the same race and have not enough kills
                    if ($npcNeeded->getRace()->getId() == $target->getRace()->getId() &&
                        $value < $npcNeeded->getNumber()
                    ) {
                        $npcs[$npcNeeded->getRace()->getId()] = $value + 1;
                    }
                }

                $playerQuest->setNpcs($npcs);

                // Check npc needed
                $npcObjects = $playerQuest->getNpcObjects();
                foreach ($quest->getNpcObjectsNeeded() as $npcObjectNeeded) {
                    $npcObject = $npcObjectNeeded->getNpcObject();
                    $names = $npcObject->getList();

                    foreach ($names as $npcName) {
                        $value = !empty($npcObjects[$npcObject->getId()]) ? $npcObjects[$npcObject->getId()] : 0;
                        // Check if name of target begins with one in the list
                        // Check for change to loot
                        if (strpos($target->getName(), $npcName) === 0 &&
                            $value < $npcObjectNeeded->getNumber() &&
                            mt_rand(0, 100) > (100 - $npcObject->getLuck())
                        ) {
                            $npcObjects[$npcObject->getId()] = $value += 1;
                        }
                    }
                }

                $playerQuest->setNpcObjects($npcObjects);
            }
        }
    }
}
