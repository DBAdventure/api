<?php

namespace Dba\GameBundle\Services;

use Dba\GameBundle\Entity\EventType;
use Dba\GameBundle\Entity\Guild;
use Dba\GameBundle\Entity\GuildEvent;
use Dba\GameBundle\Entity\Player;

class GuildService extends BaseService
{
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
        Guild $guild,
        $message,
        array $parameters = [],
        $eventType = EventType::PLAYER
    ) {
        $event = new GuildEvent();
        if (!empty($player->getId())) {
            $event->setPlayer($player);
        }

        $event->setGuild($guild);
        $event->setMessage($message);
        $event->setParameters($parameters);
        $event->setEventType($this->repos()->getEventTypeRepository()->findOneById($eventType));
        $this->em()->persist($event);
        $this->em()->flush();
    }
}
