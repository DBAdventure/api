<?php

namespace Dba\GameBundle\Services;

use Dba\GameBundle\Entity\Spell;
use Dba\GameBundle\Entity\PlayerSpell;
use Dba\GameBundle\Entity\Player;

class SpellService extends BaseService
{
    const ERROR_NOT_ENOUGH_ZENI = 1;
    const ERROR_ALREADY_PURCHASED = 2;
    const ERROR_NOT_RACE = 3;
    const ERROR_REQUIREMENTS = 4;

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
        if ($spell->getRace()->getId() != $player->getRace()->getId()) {
            return self::ERROR_NOT_RACE;
        }

        if ($player->getZeni() < $spell->getPrice()) {
            // No such money
            return self::ERROR_NOT_ENOUGH_ZENI;
        }

        $canBuy = true;
        foreach ($spell->getRequirements() as $bonus => $value) {
            $method = $this->getMethod($bonus);
            if (method_exists($player, $method)) {
                if (call_user_func(array($player, $method)) < $value) {
                    $canBuy = false;
                    break;
                }
            }
        }

        if (empty($canBuy)) {
            return self::ERROR_REQUIREMENTS;
        }

        $spellRepo = $this->repos()->getPlayerSpellRepository();
        $playerSpell = $spellRepo->findOneBy(
            [
                'spell' => $spell,
                'player' => $player
            ]
        );

        if (!empty($playerSpell)) {
            return self::ERROR_ALREADY_PURCHASED;
        }

        $playerSpell = new PlayerSpell();
        $playerSpell->setSpell($spell);
        $playerSpell->setPlayer($player);

        $player->setZeni($player->getZeni() - $spell->getPrice());
        $this->services()->getObjectService()->addZenisOnMap($player->getMap(), $spell->getPrice());

        $player->addPlayerSpell($playerSpell);
        return $playerSpell;
    }

    /**
     * Build needed method
     *
     * @param string $string String to be convert to method
     *
     * @return boolean|string
     */
    protected function getMethod($string)
    {
        return 'get' . str_replace('_', '', ucwords($string, '_'));
    }
}
