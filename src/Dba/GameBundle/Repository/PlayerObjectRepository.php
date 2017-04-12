<?php

namespace Dba\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Object;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PlayerObjectRepository extends EntityRepository
{
    /**
     * Find similar Object
     *
     * @param Player $player Player
     * @param Object $object Object
     *
     * @return PlayerObject
     */
    public function findSimilarEquipped(Player $player, Object $object)
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
     * @param Object $object Object
     *
     * @throws NotFoundHttpException
     * @return PlayerObject
     */
    public function checkPlayerObject(Player $player, Object $object)
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
