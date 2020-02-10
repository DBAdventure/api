<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\Annotations;

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
        $spells = $this->getUser()->getPlayerSpells()->toArray();
        usort(
            $spells,
            function ($a, $b) {
                $aReq = $a->getSpell()->getRequirements();
                $bReq = $b->getSpell()->getRequirements();
                $aLevel = !empty($aReq['level']) ? $aReq['level'] : 0;
                $bLevel = !empty($bReq['level']) ? $bReq['level'] : 0;

                return $aLevel > $bLevel;
            }
        );

        return $spells;
    }
}
