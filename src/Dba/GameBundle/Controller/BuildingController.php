<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Bank;
use Dba\GameBundle\Entity\Building;
use Dba\GameBundle\Entity\GameObject;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Spell;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Annotations\NamePrefix("building_")
 */
class BuildingController extends BaseController
{
    const MINIMAL_WANTED_AMOUNT = 50;
    const TELEPORT_ACTION = 5;

    protected function checkPosition($player, $building)
    {
        return $player->getX() != $building->getX() ||
            $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId() ||
            !$building->isEnabled();
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     * @Annotations\Post("/teleport/{building}")
     */
    public function postTeleportAction(Building $building, Request $request)
    {
        $where = $request->request->get('where');
        $player = $this->getUser();
        if ($this->checkPosition($player, $building) ||
            $player->getActionPoints() < self::TELEPORT_ACTION ||
            !in_array($where, Player::AVAILABLE_MOVE) ||
            $player->getForbiddenTeleport() == $where
        ) {
            return $this->forbidden();
        }

        $player->usePoints(Player::ACTION_POINT, self::TELEPORT_ACTION);
        $mapRepo = $this->repos()->getMapRepository();
        $defaultMap = $mapRepo->getDefaultMap();

        $this->services()->getPlayerService()->teleport($player, $defaultMap, $where);

        $this->em()->persist($player);
        $this->em()->flush();

        return [];
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     * @Annotations\Get("/enter/{building}")
     */
    public function getEnterAction(Building $building)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $building)) {
            return $this->forbidden();
        }

        switch ($building->getType()) {
            case Building::TYPE_TELEPORT:
                $type = 'teleport';
                break;

            case Building::TYPE_WANTED:
                $type = 'wanted';
                $objects = [
                    'minimalAmount' => self::MINIMAL_WANTED_AMOUNT,
                ];
                break;

            case Building::TYPE_BANK:
                $objects = $this->repos()->getBankRepository()->findOneByPlayer($player);
                $type = 'bank';
                break;

            case Building::TYPE_MAGIC:
                $objects = $this->repos()->getSpellRepository()->findByRace($player->getRace());
                usort(
                    $objects,
                    function ($a, $b) {
                        $aLevel = !empty($a->getRequirements()['level']) ? $a->getRequirements()['level'] : 0;
                        $bLevel = !empty($b->getRequirements()['level']) ? $b->getRequirements()['level'] : 0;

                        return $aLevel > $bLevel;
                    }
                );
                $type = 'magic';
                break;

            default:
                $objects = $this->repos()->getObjectRepository()->findBy(
                    ['type' => $building->getType(), 'enabled' => true],
                    ['price' => 'ASC']
                );
                $type = 'shop';
                break;
        }

        return [
            'building' => $building,
            'objects' => empty($objects) ? [] : $objects,
            'type' => $type,
        ];
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     * @ParamConverter("spell", class="Dba\GameBundle\Entity\Spell")
     * @Annotations\Post("/shop/{building}/spell/{spell}")
     */
    public function postBuySpellAction(Building $building, Spell $spell)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $building) ||
            $building->getType() !== Building::TYPE_MAGIC
        ) {
            return $this->forbidden();
        }

        $result = $this->services()->getSpellService()->addSpell($player, $spell);
        if (is_numeric($result)) {
            return $this->badRequest('building.magic.error.' . $result);
        }

        $this->em()->persist($player);
        $this->em()->persist($result);
        $this->em()->flush();

        return [
            'message' => 'building.magic.success',
            'parameters' => [
                'name' => sprintf('spells.%s.name', $spell->getName()),
            ],
        ];
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     * @ParamConverter("object", class="Dba\GameBundle\Entity\GameObject")
     * @Annotations\Post("/shop/{building}/object/{object}")
     */
    public function postBuyObjectAction(Building $building, GameObject $object)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $building) ||
            $object->getType() != $building->getType() ||
            !$object->isEnabled()
        ) {
            return $this->forbidden();
        }

        $result = $this->services()->getObjectService()->addToInventory($player, $object);
        if (is_numeric($result)) {
            return $this->badRequest('building.shop.error.' . $result);
        }

        $this->em()->persist($player);
        $this->em()->persist($result);
        $this->em()->flush();

        return [
            'message' => 'building.shop.success',
            'parameters' => [
                'name' => $object->getName(),
            ],
        ];
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     * @ParamConverter("object", class="Dba\GameBundle\Entity\GameObject")
     */
    public function postSellAction(Building $building, GameObject $object)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $building)) {
            return $this->forbidden();
        }

        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        // @TODO Building sell: allow to sell more than one object
        $number = 1;
        $result = $this->services()->getObjectService()->sell($playerObject, $number);
        if (is_numeric($result)) {
            return $this->badRequest('building.shop.sell.error.' . $result);
        } else {
            $this->em()->persist($playerObject);
            $this->em()->persist($player);
            $this->em()->flush();
            $this->addFlash(
                'success',
                $this->trans(
                    'building.shop.sell.success',
                    ['%object%' => $this->trans($object->getName() . '.name', [], 'objects')]
                )
            );
        }

        return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     * @Annotations\Post("/bank/{building}/deposit")
     */
    public function postDepositAction(Building $building, Request $request)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $building) ||
            $building->getType() != Building::TYPE_BANK
        ) {
            return $this->forbidden();
        }

        $zenis = ceil($request->request->get('deposit'));
        $goldBar = floor($zenis / 20);
        if ($player->getZeni() < $zenis || empty($zenis) || $zenis < 1 || empty($goldBar)) {
            return $this->badRequest('building.bank.deposit.error');
        }

        $player->setZeni($player->getZeni() - ($goldBar * 20));
        $bankPlayer = $this->repos()->getBankRepository()->findOneByPlayer($player);
        if (empty($bankPlayer)) {
            $bankPlayer = new Bank();
            $bankPlayer->setPlayer($player);
        }

        $goldBank = $bankPlayer->getZeni() + $goldBar;
        $bankPlayer->setZeni($goldBank);

        $this->em()->persist($bankPlayer);
        $this->em()->persist($player);
        $this->em()->flush();

        return [
            'message' => 'building.bank.deposit.zeni',
            'parameters' => [
                'goldBar' => $goldBar,
                'goldBank' => $goldBank,
            ],
        ];
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     * @Annotations\Post("/bank/{building}/withdraw")
     */
    public function postWithdrawAction(Building $building, Request $request)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $building) ||
            $building->getType() != Building::TYPE_BANK
        ) {
            return $this->forbidden();
        }

        $withdraw = round($request->request->get('withdraw'));
        $bankPlayer = $this->repos()->getBankRepository()->findOneByPlayer($player);
        if (empty($bankPlayer) || $withdraw <= 0 || $withdraw > $bankPlayer->getZeni()) {
            return $this->badRequest('building.bank.withdraw.error');
        }

        $goldBank = $bankPlayer->getZeni() - $withdraw;
        $bankPlayer->setZeni($goldBank);
        $player->setZeni($player->getZeni() + ($withdraw * 18));

        $this->em()->persist($bankPlayer);
        $this->em()->persist($player);
        $this->em()->flush();

        return [
            'message' => 'building.bank.withdraw.zeni',
            'parameters' => [
                'goldBar' => $withdraw,
                'goldBank' => $goldBank,
            ],
        ];
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     * @Annotations\Post("/wanted/{building}")
     */
    public function postWantedAction(Building $building, Request $request)
    {
        $player = $this->getUser();
        if ($this->checkPosition($player, $building) ||
            $building->getType() != Building::TYPE_WANTED
        ) {
            return $this->forbidden();
        }

        $amount = (int) $request->request->get('amount');
        $target = $this->repos()->getPlayerRepository()->findOneByName($request->request->get('target'));
        if (empty($target) || $amount < self::MINIMAL_WANTED_AMOUNT || $amount > $player->getZeni()) {
            return $this->badRequest('building.wanted.error');
        }

        $target->setHeadPrice($target->getHeadPrice() + $amount);
        $player->setZeni($player->getZeni() - $amount);

        $this->em()->persist($target);
        $this->em()->persist($player);
        $this->em()->flush();

        return [
            'message' => 'building.wanted.zeni',
            'parameters' => [
                'amount' => $amount,
                'headPrice' => $target->getHeadPrice(),
            ],
        ];
    }
}
