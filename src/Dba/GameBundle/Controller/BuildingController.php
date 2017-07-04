<?php

namespace Dba\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Spell;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Building;
use Dba\GameBundle\Entity\Bank;
use Dba\GameBundle\Services\ObjectService;

/**
 * @Route("/building")
 */
class BuildingController extends BaseController
{
    const MINIMAL_WANTED_AMOUNT = 50;
    const TELEPORT_ACTION = 5;

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     *
     * @return JsonResponse
     */
    public function postTeleportAction(Building $building, $where)
    {
        $player = $this->getUser();
        if ($player->getX() != $building->getX() || $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId() ||
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

        return new JsonResponse();
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     *
     * @return JsonResponse
     */
    public function getEnterAction(Building $building)
    {
        $player = $this->getUser();
        if ($player->getX() != $building->getX() || $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId()
        ) {
            return $this->forbidden();
        }

        switch ($building->getType()) {
            case Building::TYPE_TELEPORT:
                $template = 'teleport.html.twig';
                break;

            case Building::TYPE_WANTED:
                $template = 'wanted.html.twig';
                $objects = [
                    'minimalAmount' => self::MINIMAL_WANTED_AMOUNT,
                ];
                break;

            case Building::TYPE_BANK:
                $objects = $this->repos()->getBankRepository()->findOneByPlayer($player);
                $template = 'bank.html.twig';
                break;

            case Building::TYPE_MAGIC:
                $objects = $this->repos()->getSpellRepository()->findByRace($player->getRace());
                $template = 'magic.html.twig';
                break;

            default:
                $objects = $this->repos()->getObjectRepository()->findByType($building->getType(), ['price' => 'ASC']);
                $template = 'shop.html.twig';
                break;
        }

        return new JsonResponse(
            [
                'building' => $building,
                'objects' => empty($objects) ? [] : $objects
            ]
        );
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building", options={"id" = "building_id"})
     * @ParamConverter("spell", class="Dba\GameBundle\Entity\Spell", options={"id" = "spell_id"})
     */
    public function postBuySpellAction(Building $building, Spell $spell)
    {
        $player = $this->getUser();
        if ($player->getX() != $building->getX() || $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId() ||
            $spell->getRace()->getId() != $player->getRace()->getId() ||
            $spell->getPrice() > $player->getZeni()
        ) {
            return $this->forbidden();
        }

        $result = $this->services()->getSpellService()->addSpell($player, $spell);
        if (is_numeric($result)) {
            $this->addFlash(
                'danger',
                $this->trans('building.magic.error.' . $result)
            );
        } else {
            $this->em()->persist($player);
            $this->em()->persist($result);
            $this->em()->flush();
            $this->addFlash(
                'success',
                $this->trans(
                    'building.magic.success',
                    ['%object%' => $this->trans($spell->getName() . '.name', [], 'objects')]
                )
            );
        }

        return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
    }

    /**
     * @Route("/shop/{building_id}/buy/{object_id}", name="building.shop.buy", methods="GET",
              requirements={"building_id": "\d+", "object_id": "\d+"})
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building", options={"id" = "building_id"})
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object", options={"id" = "object_id"})
     * @Template()
     */
    public function buyAction(Building $building, Object $object)
    {
        $player = $this->getUser();
        if ($player->getX() != $building->getX() || $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId() ||
            $object->getType() != $building->getType()
        ) {
            return $this->forbidden();
        }

        $result = $this->services()->getObjectService()->addToInventory($player, $object);
        if (is_numeric($result)) {
            $this->addFlash(
                'danger',
                $this->trans('building.shop.error.' . $result)
            );
        } else {
            $this->em()->persist($player);
            $this->em()->persist($result);
            $this->em()->flush();
            $this->addFlash(
                'success',
                $this->trans(
                    'building.shop.success',
                    ['%object%' => $this->trans($object->getName() . '.name', [], 'objects')]
                )
            );
        }

        return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
    }


    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building", options={"id" = "building_id"})
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object", options={"id" = "object_id"})
     */
    public function postSellAction(Building $building, Object $object)
    {
        $player = $this->getUser();
        if ($player->getX() != $building->getX() || $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId()
        ) {
            return $this->forbidden();
        }

        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        // @TODO Building sell: allow to sell more than one object
        $number = 1;
        $result = $this->services()->getObjectService()->sell($playerObject, $number);
        if (is_numeric($result)) {
            $this->addFlash(
                'danger',
                $this->trans('building.shop.sell.error.' . $result)
            );
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
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building", options={"id" = "building_id"})
     */
    public function postDepositAction(Building $building, Request $request)
    {
        $player = $this->getUser();
        if ($player->getX() != $building->getX() || $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId() ||
            $building->getType() != Building::TYPE_BANK
        ) {
            return $this->forbidden();
        }

        $zenis = ceil($request->request->get('deposit'));
        $goldBar = floor($zenis / 20);
        if ($player->getZeni() < $zenis || empty($zenis) || $zenis < 1 || empty($goldBar)) {
            $this->addFlash(
                'danger',
                $this->trans(
                    'building.bank.deposit.error'
                )
            );

            return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
        }

        $player->setZeni($player->getZeni() - ($goldBar * 20));
        $bankPlayer = $this->repos()->getBankRepository()->findOneByPlayer($player);
        if (empty($bankPlayer)) {
            $bankPlayer = new Bank();
            $bankPlayer->setPlayer($player);
        }

        $goldBank = $bankPlayer->getZeni() + $goldBar;
        $bankPlayer->setZeni($goldBank);

        $this->addFlash(
            'success',
            $this->trans(
                'building.bank.deposit.zeni',
                [
                    '%goldBar%' => $goldBar,
                    '%goldBank%' => $goldBank,
                ]
            )
        );

        $this->em()->persist($bankPlayer);
        $this->em()->persist($player);
        $this->em()->flush();

        return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
    }

    /**
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building", options={"id" = "building_id"})
     * @param Request $request Request
     */
    public function postWithdrawAction(Building $building, Request $request)
    {
        $player = $this->getUser();
        if ($player->getX() != $building->getX() || $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId() ||
            $building->getType() != Building::TYPE_BANK
        ) {
            return $this->forbidden();
        }

        $withdraw = round($request->request->get('withdraw'));
        $bankPlayer = $this->repos()->getBankRepository()->findOneByPlayer($player);
        if (empty($bankPlayer) || $withdraw > $bankPlayer->getZeni()) {
            $this->addFlash(
                'danger',
                $this->trans(
                    'building.bank.withdraw.error'
                )
            );

            return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
        }

        $goldBank = $bankPlayer->getZeni() - $withdraw;
        $bankPlayer->setZeni($goldBank);
        $player->setZeni($player->getZeni() + ($withdraw * 18));

        $this->addFlash(
            'success',
            $this->trans(
                'building.bank.withdraw.zeni',
                [
                    '%goldBar%' => $withdraw,
                    '%goldBank%' => $goldBank,
                ]
            )
        );

        $this->em()->persist($bankPlayer);
        $this->em()->persist($player);
        $this->em()->flush();

        return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
    }

    /**
     * @Route("/wanted/{building_id}", name="building.wanted", methods="POST",
              requirements={"building_id": "\d+"})
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building", options={"id" = "building_id"})
     * @param Request $request Request
     * @Template()
     */
    public function wantedAction(Building $building, Request $request)
    {
        $player = $this->getUser();
        if ($player->getX() != $building->getX() || $player->getY() != $building->getY() ||
            $player->getMap()->getId() != $building->getMap()->getId() ||
            $building->getType() != Building::TYPE_WANTED
        ) {
            return $this->forbidden();
        }

        $amount = (int) $request->request->get('amount');
        $target = $this->repos()->getPlayerRepository()->findOneByName($request->request->get('target'));
        if (empty($target) || $amount < self::MINIMAL_WANTED_AMOUNT || $amount > $player->getZeni()) {
            $this->addFlash(
                'danger',
                $this->trans(
                    'building.wanted.error'
                )
            );

            return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
        }

        $target->setHeadPrice($target->getHeadPrice() + $amount);
        $player->setZeni($player->getZeni() - $amount);

        $this->addFlash(
            'success',
            $this->trans(
                'building.wanted.zeni',
                [
                    '%amount%' => $amount,
                    '%headPrice%' => $target->getHeadPrice(),
                ]
            )
        );

        $this->em()->persist($target);
        $this->em()->persist($player);
        $this->em()->flush();

        return $this->redirect($this->generateUrl('building.enter', ['id' => $building->getId()]));
    }
}
