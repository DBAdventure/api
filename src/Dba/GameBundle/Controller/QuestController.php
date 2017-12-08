<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Quest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

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
