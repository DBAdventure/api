<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Quest;
use FOS\RestBundle\Controller\Annotations;

/**
 * @Annotations\NamePrefix("quest_")
 */
class QuestController extends BaseController
{
    /**
     * @Annotations\Get("/player")
     */
    public function getQuestsAction()
    {
        return [
            'player_quests' => $this->getUser()->getPlayerQuests(),
            'player_objects' => $this->services()->getPlayerService()->getAvailableObjects($this->getUser()),
        ];
    }
}
