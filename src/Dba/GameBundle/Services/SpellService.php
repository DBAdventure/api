<?php

namespace Dba\GameBundle\Services;

use Dba\GameBundle\Entity\Spell;
use Dba\GameBundle\Entity\PlayerSpell;
use Dba\GameBundle\Entity\Player;

class SpellService extends BaseService
{
    const SPELL_ALREADY_PURCHASED = 1;
    /*
     * Add spell into player
     *
     * @param Player $player Player who buy
     * @param Spell $spell Spell to buy
     *
     * @return integer|PlayerSpell
     */
    public function addSpell(Player $player, Spell $spell)
    {
        $objectRepo = $this->repos()->getPlayerSpellRepository();
        $playerSpell = $objectRepo->findOneBy(
            [
                'spell' => $spell,
                'player' => $player
            ]
        );

        if (!empty($playerSpell)) {
            return self::SPELL_ALREADY_PURCHASED;
        }

        $playerSpell = new PlayerSpell();
        $playerSpell->setSpell($spell);
        $playerSpell->setPlayer($player);

        $player->setZeni($player->getZeni() - $spell->getPrice());
        $this->services()->getObjectService()->addZenisOnMap($player->getMap(), $spell->getPrice());

        $player->addPlayerSpell($playerSpell);
        return $playerSpell;
    }
}
