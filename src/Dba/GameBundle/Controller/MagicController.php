<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Annotations\NamePrefix("magic_")
 */
class MagicController extends BaseController
{
    /**
     * @Annotations\Get("/spells")
     */
    public function getSpellsAction()
    {
        return $this->getUser()->getPlayerSpells();
    }
}
