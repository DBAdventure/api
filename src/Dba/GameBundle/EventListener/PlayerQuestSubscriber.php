<?php

namespace Dba\GameBundle\EventListener;

use DateTime;
use Dba\GameBundle\Services\ServicesService;
use Dba\GameBundle\Event\DbaEvents;
use Dba\GameBundle\Event\ActionEvent;
use Dba\GameBundle\Entity\Side;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PlayerQuestSubscriber implements EventSubscriberInterface
{
    protected $services;

    /**
     * Constructor
     *
     * @param ServicesService $services Services
     *
     */
    public function __construct(ServicesService $services)
    {
        $this->services = $services;
    }


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
        $messages = &$event->getData()['messages'];
        $target = $event->getTarget();
        if (empty($event->getData()['isDead']) || $target->getSide()->getId() !== Side::NPC) {
            return;
        }

        $player = $event->getPlayer();
        $playerQuests = $player->getPlayerQuests()->filter(
            function ($entity) {
                return $entity->isInProgress();
            }
        );

        foreach ($playerQuests as $playerQuest) {
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
                    $messages[] = [
                        'message' => 'game.quest.npc.kill',
                        'parameters' => [
                            'name' => $npcNeeded->getRace()->getName(),
                            'questName' => $quest->getName(),
                        ]
                    ];
                }
            }

            $playerQuest->setNpcs($npcs);

            // Check npc needed
            $npcObjects = $playerQuest->getNpcObjects();
            foreach ($quest->getNpcObjectsNeeded() as $npcObjectNeeded) {
                $npcObject = $npcObjectNeeded->getNpcObject();
                $value = !empty($npcObjects[$npcObject->getId()]) ? $npcObjects[$npcObject->getId()] : 0;
                // Check if name of target begins with one in the list
                // Check for change to loot
                if ($npcObject->getRaces()->contains($target->getRace()) &&
                    $value < $npcObjectNeeded->getNumber()
                ) {
                    if (mt_rand(0, 100) > (100 - $npcObject->getLuck())) {
                        $npcObjects[$npcObject->getId()] = $value += 1;
                        $messages[] = [
                            'message' => 'game.quest.npc.object.found',
                            'parameters' => [
                                'name' => $npcObject->getName(),
                                'questName' => $quest->getName(),
                            ]
                        ];
                    } else {
                        $messages[] = [
                            'message' => 'game.quest.npc.object.notFound',
                            'parameters' => [
                                'name' => $npcObject->getName(),
                                'questName' => $quest->getName(),
                            ]
                        ];
                    }
                }
            }

            $playerQuest->setNpcObjects($npcObjects);
        }

        $this->services->getQuestService()->canBeDone($playerQuest, $messages);
    }
}
