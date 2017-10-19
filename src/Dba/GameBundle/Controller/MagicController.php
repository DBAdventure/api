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
        if (!$this->getUser()) {
            return $this->forbidden();
        }

        return $this->getUser()->getPlayerSpells();
    }
}
