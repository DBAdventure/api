<?php

namespace Dba\GameBundle\EventListener;

use DateTime;
use Dba\GameBundle\Entity\Player;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class PlayerSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad,
        ];
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $this->calculatePoints($args);
    }

    public function calculatePoints(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!($entity instanceof Player) || !$entity->isEnabled()) {
            return;
        }

        $data = [
            Player::ACTION_POINT,
            Player::MOVEMENT_POINT,
            Player::FATIGUE_POINT,
            Player::KI_POINT,
        ];

        foreach ($data as $what) {
            switch ($what) {
                case Player::ACTION_POINT:
                    $method = 'Action';
                    $pointTime = Player::TIME_ACTION_POINT;
                    break;
                case Player::MOVEMENT_POINT:
                    $method = 'Movement';
                    $pointTime = Player::TIME_MOVEMENT_POINT;
                    break;
                case Player::FATIGUE_POINT:
                    $method = 'Fatigue';
                    $pointTime = Player::TIME_FATIGUE_POINT;
                    break;
                case Player::KI_POINT:
                    $method = 'Ki';
                    $pointTime = Player::TIME_KI_POINT;
                    break;
            }

            $currentDate = new DateTime();
            $originalTime = $entity->{'get' . $method . 'UpdatedAt'}();

            $points = floor(($currentDate->getTimestamp() - $originalTime->getTimestamp()) / (60 * $pointTime));
            if (empty($points)) {
                continue;
            }

            $entity->{'set' . $method . 'UpdatedAt'}(
                $currentDate->setTimestamp(
                    $originalTime->getTimestamp() + ($points * (60 * $pointTime))
                )
            );
            if ($what == Player::FATIGUE_POINT) {
                $entity->usePoints($what, $points);
            } else {
                $entity->addPoints($what, $points);
            }
        }

        $args->getEntityManager()->persist($entity);
        $args->getEntityManager()->flush();
    }
}
