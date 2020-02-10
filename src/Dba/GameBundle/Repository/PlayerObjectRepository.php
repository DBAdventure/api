<?php

namespace Dba\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\GameObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlayerObjectRepository extends EntityRepository
{
    /**
     * Find similar GameObject
     *
     * @param Player $player Player
     * @param GameObject $object GameObject
     *
     * @return PlayerObject
     */
    public function findSimilarEquipped(Player $player, GameObject $object)
    {
        $playerObjects = $this->findBy(
            [
                'player' => $player
            ]
        );

        foreach ($playerObjects as $playerObject) {
            if ($playerObject->getObject()->getType() == $object->getType() && $playerObject->isEquipped()) {
                return $playerObject;
            }
        }

        return false;
    }

    /**
     * Check if player has an object
     *
     * @param Player $player Player
     * @param GameObject $object GameObject
     *
     * @throws NotFoundHttpException
     * @return PlayerObject
     */
    public function checkPlayerObject(Player $player, GameObject $object)
    {
        $playerObject = $this->findOneBy(
            [
                'player' => $player,
                'object' => $object
            ]
        );

        if (empty($playerObject) or empty($playerObject->getNumber())) {
            throw new NotFoundHttpException('Object not found.');
        }

        return $playerObject;
    }
}
