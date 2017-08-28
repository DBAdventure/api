<?php

namespace Dba\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\PlayerObject;

/**
 * @Route("/inventory")
 */
class InventoryController extends BaseController
{
    const TELEPORT_MOVEMENT_POINTS = 10;

    public function getObjectsAction()
    {
        $objects = [];
        $playerObjects = $this->getUser()->getPlayerObjects();
        foreach ($playerObjects as $playerObject) {
            if (empty($playerObject->getNumber())) {
                continue;
            }

            $objects[$playerObject->getObject()->getType()][] = $playerObject;
        }

        return $objects;
    }

    /**
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object")
     */
    public function postUseAction(Request $request, Object $object)
    {
        $player = $this->getUser();
        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        if (!$playerObject->canBeUsed()) {
            return $this->forbidden($this->trans('inventory.use.cant'));
        }

        $nbObjectsUsed = (int) $request->request->get('nb', 1);
        if ($playerObject->getNumber() < $nbObjectsUsed || $nbObjectsUsed < 0) {
            $nbObjectsUsed = $playerObject->getNumber();
        }

        foreach ($playerObject->getObject()->getBonus() as $bonus => $value) {
            switch ($bonus) {
                case Object::BONUS_HEALTH_PERCENT:
                    $player->addPoints(
                        Player::HEALTH_POINT,
                        ceil((($value * $nbObjectsUsed) * $player->getTotalMaxHealth()) / 100)
                    );
                    break;
                case Object::BONUS_HEALTH:
                    $player->addPoints(Player::HEALTH_POINT, ($value * $nbObjectsUsed));
                    break;
                case Object::BONUS_ACTION_POINT:
                    $player->addPoints(Player::ACTION_POINT, ($value * $nbObjectsUsed));
                    break;
                case Object::BONUS_MOVEMENT_POINT:
                    $player->addPoints(Player::MOVEMENT_POINT, ($value * $nbObjectsUsed));
                    break;
                case Object::BONUS_KI:
                    $player->addPoints(Player::KI_POINT, ($value * $nbObjectsUsed));
                    break;
                case Object::BONUS_KI_PERCENT:
                    $player->addPoints(
                        Player::KI_POINT,
                        ceil((($value * $nbObjectsUsed) * $player->getTotalMaxKi()) / 100)
                    );
                    break;
                case Object::BONUS_FATIGUE:
                    $player->usePoints(Player::FATIGUE_POINT, ($value * $nbObjectsUsed));
                    break;
                case Object::BONUS_FATIGUE_PERCENT:
                    $player->usePoints(
                        Player::FATIGUE_POINT,
                        ceil((($value * $nbObjectsUsed) * $player->getMaxFatigue()) / 100)
                    );
                    break;

                case Object::BONUS_TELEPORT:
                    if ($player->getMovementPoints() < self::TELEPORT_MOVEMENT_POINTS) {
                        $this->addFlash(
                            'danger',
                            $this->trans('object.error.teleport')
                        );

                        return $this->redirect($this->generateUrl('inventory'));
                    }

                    $player->usePoints(Player::MOVEMENT_POINT, self::TELEPORT_MOVEMENT_POINTS);
                    $mapRepo = $this->repos()->getMapRepository();
                    $this->services()->getPlayerService()->teleport($player, $mapRepo->getDefaultMap(), $value);
                    break;
            }
        }

        $playerObject->setNumber($playerObject->getNumber() - $nbObjectsUsed);
        $this->em()->persist($player);
        $this->em()->persist($playerObject);
        $this->em()->flush();

        return [
            'message' => $this->trans(
                'object.used',
                [
                    '%number%' => $nbObjectsUsed,
                    '%name%' => $this->trans($playerObject->getObject()->getName() . '.name', [], 'objects')
                ]
            )
        ];
    }

    /**
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object")
     */
    public function postDropAction(Object $object)
    {
        $player = $this->getUser();
        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        if (!$playerObject->canBeDropped()) {
            return $this->forbidden($this->trans('inventory.drop.cant'));
        }

        $nbObjectsDropped = (int) $request->request->get('nb', 1);
        if ($playerObject->getNumber() < $nbObjectsUsed || $nbObjectsUsed < 0) {
            $nbObjectsDropped = $playerObject->getNumber();
        }

        $this->services()->getObjectService()->drop($playerObject, $nbObjectsDropped);
        $playerObject->setNumber($playerObject->getNumber() - $nbObjectsDropped);
        $playerObject->setEquipped(false);
        $this->em()->persist($playerObject);
        $this->em()->flush();
        return [
            'message' => $this->trans(
                'object.drop',
                ['%name%' => $this->trans($playerObject->getObject()->getName() . '.name', [], 'objects')]
            )
        ];
    }

    /**
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object")
     */
    public function postUnequipAction(Object $object)
    {
        $player = $this->getUser();
        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        $playerObject->setEquipped(false);
        $this->em()->persist($playerObject);
        $this->em()->flush();
        return [
            'message' => $this->trans(
                'object.unequip',
                ['%name%' => $this->trans($playerObject->getObject()->getName() . '.name', [], 'objects')]
            )
        ];
    }

    /**
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object")
     */
    public function postEquipAction(Object $object)
    {
        $player = $this->getUser();
        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        if (!$playerObject->canBeEquipped()) {
            return $this->forbidden($this->trans('inventory.equip.cant'));
        }

        $canEquip = true;
        foreach ($playerObject->getObject()->getRequirements() as $bonus => $value) {
            $method = $this->getMethod($bonus);
            if (method_exists($player, $method)) {
                if (call_user_func(array($player, $method)) < $value) {
                    $canEquip = false;
                    break;
                }
            }
        }

        if (!$canEquip) {
            return $this->forbidden(
                $this->trans(
                    'object.error.equip',
                    ['%name%' => $this->trans($playerObject->getObject()->getName() . '.name', [], 'objects')]
                )
            );
        }

        $similarObject = $this->repos()->getPlayerObjectRepository()->findSimilarEquipped(
            $player,
            $object
        );

        $messages = [];
        if (!empty($similarObject)) {
            $messages[] = [
                $this->trans(
                    'object.unequip',
                    ['%name%' => $this->trans($similarObject->getObject()->getName() . '.name', [], 'objects')]
                )
            ];
            $similarObject->setEquipped(false);
            $this->em()->persist($similarObject);
        }

        $messages[] = [
            $this->trans(
                'object.equip',
                ['%name%' => $this->trans($object->getName() . '.name', [], 'objects')]
            )
        ];
        $playerObject->setEquipped(true);
        $this->em()->persist($playerObject);
        $this->em()->flush();

        return [
            'messages' => $messages
        ];
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
