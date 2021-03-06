<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\GameObject;
use Dba\GameBundle\Entity\Player;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Annotations\NamePrefix("inventory_")
 */
class InventoryController extends BaseController
{
    const TELEPORT_MOVEMENT_POINTS = 10;

    /**
     * @Annotations\Get("/objects")
     */
    public function getObjectsAction()
    {
        return $this->services()->getPlayerService()->getAvailableObjects($this->getUser());
    }

    /**
     * @ParamConverter("object", class="Dba\GameBundle\Entity\GameObject")
     * @Annotations\Post("/use/{object}")
     */
    public function postUseAction(Request $request, GameObject $object)
    {
        if (!$object->isEnabled()) {
            return $this->forbidden();
        }

        $player = $this->getUser();
        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        if (!$playerObject->canBeUsed()) {
            return $this->forbidden('inventory.object.cant.use');
        }

        $nbObjectsUsed = (int) $request->request->get('nb', 1);
        if ($playerObject->getNumber() < $nbObjectsUsed || $nbObjectsUsed < 0) {
            $nbObjectsUsed = $playerObject->getNumber();
        }

        foreach ($playerObject->getObject()->getBonus() as $bonus => $value) {
            switch ($bonus) {
                case GameObject::BONUS_HEALTH_PERCENT:
                    $player->addPoints(
                        Player::HEALTH_POINT,
                        ceil((($value * $nbObjectsUsed) * $player->getTotalMaxHealth()) / 100)
                    );
                    break;
                case GameObject::BONUS_HEALTH:
                    $player->addPoints(Player::HEALTH_POINT, ($value * $nbObjectsUsed));
                    break;
                case GameObject::BONUS_ACTION_POINT:
                    $player->addPoints(Player::ACTION_POINT, ($value * $nbObjectsUsed));
                    break;
                case GameObject::BONUS_MOVEMENT_POINT:
                    $player->addPoints(Player::MOVEMENT_POINT, ($value * $nbObjectsUsed));
                    break;
                case GameObject::BONUS_KI:
                    $player->addPoints(Player::KI_POINT, ($value * $nbObjectsUsed));
                    break;
                case GameObject::BONUS_KI_PERCENT:
                    $player->addPoints(
                        Player::KI_POINT,
                        ceil((($value * $nbObjectsUsed) * $player->getTotalMaxKi()) / 100)
                    );
                    break;
                case GameObject::BONUS_FATIGUE:
                    $player->usePoints(Player::FATIGUE_POINT, ($value * $nbObjectsUsed));
                    break;
                case GameObject::BONUS_FATIGUE_PERCENT:
                    $player->usePoints(
                        Player::FATIGUE_POINT,
                        ceil((($value * $nbObjectsUsed) * $player->getMaxFatiguePoints()) / 100)
                    );
                    break;

                case GameObject::BONUS_TELEPORT:
                    if ($player->getMovementPoints() < self::TELEPORT_MOVEMENT_POINTS) {
                        return $this->forbidden('inventory.object.error.teleport');
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
            'message' => 'inventory.object.used',
            'parameters' => [
                'number' => $nbObjectsUsed,
                'name' => $playerObject->getObject()->getName(),
            ],
        ];
    }

    /**
     * @ParamConverter("object", class="Dba\GameBundle\Entity\GameObject")
     * @Annotations\Post("/drop/{object}")
     */
    public function postDropAction(Request $request, GameObject $object)
    {
        $player = $this->getUser();
        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        if (!$playerObject->canBeDropped()) {
            return $this->forbidden('inventory.object.cant.drop');
        }

        $nbObjectsDropped = (int) $request->request->get('nb', 1);
        if ($playerObject->getNumber() < $nbObjectsDropped || $nbObjectsDropped < 0) {
            $nbObjectsDropped = $playerObject->getNumber();
        }

        $this->services()->getObjectService()->drop($playerObject, $nbObjectsDropped);
        $playerObject->setNumber($playerObject->getNumber() - $nbObjectsDropped);
        $playerObject->setEquipped(false);
        $this->em()->persist($playerObject);
        $this->em()->flush();

        return [
            'message' => 'inventory.object.drop',
            'parameters' => [
                'name' => $playerObject->getObject()->getName(),
            ],
        ];
    }

    /**
     * @ParamConverter("object", class="Dba\GameBundle\Entity\GameObject")
     * @Annotations\Post("/unequip/{object}")
     */
    public function postUnequipAction(object $object)
    {
        if (!$object->isEnabled()) {
            return $this->forbidden();
        }

        $player = $this->getUser();
        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        $playerObject->setEquipped(false);
        $this->em()->persist($playerObject);
        $this->em()->flush();

        return [
            'message' => 'inventory.object.unequip',
            'parameters' => [
                'name' => $playerObject->getObject()->getName(),
            ],
        ];
    }

    /**
     * @ParamConverter("object", class="Dba\GameBundle\Entity\GameObject")
     * @Annotations\Post("/equip/{object}")
     */
    public function postEquipAction(object $object)
    {
        if (!$object->isEnabled()) {
            return $this->forbidden();
        }

        $player = $this->getUser();
        $playerObject = $this->repos()->getPlayerObjectRepository()->checkPlayerObject($player, $object);
        if (!$playerObject->canBeEquipped()) {
            return $this->forbidden('inventory.object.cant.equip');
        }

        $canEquip = true;
        foreach ($playerObject->getObject()->getRequirements() as $bonus => $value) {
            $method = $this->getMethod($bonus);
            if (method_exists($player, $method)) {
                if (call_user_func([$player, $method]) < $value) {
                    $canEquip = false;
                    break;
                }
            }
        }

        if (!$canEquip) {
            return $this->forbidden([
                'message' => 'inventory.object.error.equip',
                'parameters' => [
                    'name' => $playerObject->getObject()->getName(),
                ],
            ]);
        }

        $similarObject = $this->repos()->getPlayerObjectRepository()->findSimilarEquipped(
            $player,
            $object
        );

        $messages = [];
        if (!empty($similarObject)) {
            $messages[] = [
                'message' => 'inventory.object.unequip',
                'parameters' => [
                    'name' => $similarObject->getObject()->getName(),
                ],
            ];
            $similarObject->setEquipped(false);
            $this->em()->persist($similarObject);
        }

        $messages[] = [
            'message' => 'inventory.object.equip',
            'parameters' => [
                'name' => $playerObject->getObject()->getName(),
            ],
        ];
        $playerObject->setEquipped(true);
        $this->em()->persist($playerObject);
        $this->em()->flush();

        return [
            'messages' => $messages,
        ];
    }

    /**
     * Build needed method
     *
     * @param string $string String to be convert to method
     *
     * @return bool|string
     */
    protected function getMethod($string)
    {
        return 'get' . str_replace('_', '', ucwords($string, '_'));
    }
}
