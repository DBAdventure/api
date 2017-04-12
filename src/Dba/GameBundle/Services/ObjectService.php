<?php

namespace Dba\GameBundle\Services;

use Dba\GameBundle\Entity\Map;
use Dba\GameBundle\Entity\MapObject;
use Dba\GameBundle\Entity\MapObjectType;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\PlayerObject;

class ObjectService extends BaseService
{
    const ERROR_INVENTORY_FULL = 1;
    const ERROR_NOT_ENOUGH_ZENI = 2;
    const ERROR_ALREADY_PURCHASED = 3;
    const ERROR_SELL_NO_ITEM = 4;

    /*
     * Add object into player inventory
     *
     * @param Player $player Player who buy
     * @param Object $object Object to buy
     * @param boolean $isPurchased Is the item is purchased or not
     * @param integer $number Number of item
     *
     * @return integer|PlayerObject
     */
    public function addToInventory(Player $player, Object $object, $isPurchased = true, $number = 1)
    {
        if (($object->getWeight() + $player->getInventoryWeight()) >  $player->getInventoryMaxWeight()) {
            return self::ERROR_INVENTORY_FULL;
        }

        if ($isPurchased && $player->getZeni() < $object->getPrice()) {
            // No such money
            return self::ERROR_NOT_ENOUGH_ZENI;
        }

        $objectRepo = $this->repos()->getPlayerObjectRepository();
        $playerObject = $objectRepo->findOneBy(
            [
                'object' => $object,
                'player' => $player
            ]
        );

        if (empty($playerObject)) {
            $playerObject = new PlayerObject();
            $playerObject->setObject($object);
            $playerObject->setPlayer($player);
            $playerObject->setNumber($number);
            $playerObject->setEquipped(false);
        } else {
            if ($object->getType() == Object::TYPE_CONSUMABLE || $playerObject->getNumber() == 0) {
                $playerObject->setNumber($playerObject->getNumber() + $number);
            } else {
                return self::ERROR_ALREADY_PURCHASED;
            }
        }

        if ($isPurchased) {
            $player->setZeni($player->getZeni() - $object->getPrice());
            $this->addZenisOnMap($player->getMap(), $object->getPrice());
        }

        $player->addPlayerObject($playerObject);
        return $playerObject;
    }

    /*
     * Create new object onthe map
     *
     * @param MapObject $mapObject The map object who need to be duplicated
     *
     * @return boolean
     */
    public function cloneMapObject(MapObject $mapObject)
    {
        $mapRepo = $this->repos()->getMapRepository();
        $map = $mapObject->getMap();
        $newPosition = $mapRepo->findPosition($map, 1, $map->getMaxX(), 1, $map->getMaxY());

        $newMapObject = new MapObject();
        $newMapObject->setMap($map);
        $newMapObject->setX($newPosition['x']);
        $newMapObject->setY($newPosition['y']);
        $newMapObject->setObject($mapObject->getObject());
        $newMapObject->setMapObjectType($mapObject->getMapObjectType());
        switch ($mapObject->getMapObjectType()->getId()) {
            case MapObjectType::BUSH:
                $newMapObject->setNumber(mt_rand(1, 5));
                break;
            case MapObjectType::CAPSULE_BLUE:
            case MapObjectType::CAPSULE_RED:
                $newMapObject->setNumber(mt_rand(1, 100));
                break;
            default:
                $newMapObject->setNumber($newMapObject->getNumber());
        }

        $this->em()->refresh($mapObject);
        $this->em()->persist($newMapObject);

        return true;
    }


    /*
     * Open capsule
     *
     * @param MapObject $mapObject The capsule object who need to be opened
     *
     * @return boolean
     */
    public function openCapsule(MapObject $mapObject)
    {
        $number = $mapObject->getNumber();
        if ($mapObject->getMapObjectType()->getId() == MapObjectType::CAPSULE_BLUE) {
            if ($number < 50) {
                $damagePercent = mt_rand(2, 10);
            } elseif ($number >= 50 && $number < 85) {
                $object = ['id' => Object::DEFAULT_PEAR, 'quantity' => 5];
            } elseif ($number >= 85 && $number < 95) {
                $object = ['id' => Object::DEFAULT_POTION_OF_LIFE, 'quantity' => 2];
            } else {
                $object = ['id' => Object::DEFAULT_SENZU, 'quantity' => 1];
            }
        } else {
            if ($number < 75) {
                $damagePercent = mt_rand(5, 25);
            } elseif ($number >= 75 && $number < 85) {
                $object = ['id' => Object::DEFAULT_PEAR, 'quantity' => 10];
            } elseif ($number >= 85 && $number < 95) {
                $object = ['id' => Object::DEFAULT_POTION_OF_LIFE, 'quantity' => 5];
            } else {
                $object = ['id' => Object::DEFAULT_SENZU, 'quantity' => 2];
            }
        }

        if (!empty($object)) {
            $object['entity'] = $this->repos()->getObjectRepository()->findOneById($object['id']);
        }

        return [
            'damages' => empty($damagePercent) ? null : $damagePercent,
            'object' => empty($object) ? null : $object,
        ];
    }

    /**
     * Drop item on the map
     *
     * @param PlayerObject $playerObject Player object
     * @param integer $distance Distance
     *
     * @return boolean
     */
    public function drop(PlayerObject $playerObject, $distance = 0)
    {
        if (empty($distance)) {
            $newPosition = [
                'x' => $playerObject->getPlayer()->getX(),
                'y' => $playerObject->getPlayer()->getY()
            ];
        } else {
            $mapRepo = $this->repos()->getMapRepository();
            $player = $playerObject->getPlayer();
            $newPosition = $mapRepo->findPosition(
                $player->getMap(),
                $player->getX() - $distance,
                $player->getX() + $distance,
                $player->getY() - $distance,
                $player->getY() + $distance
            );
        }

        $newMapObject = new MapObject();
        $newMapObject->setMap($playerObject->getPlayer()->getMap());
        $newMapObject->setX($newPosition['x']);
        $newMapObject->setY($newPosition['y']);
        $newMapObject->setObject($playerObject->getObject());
        $newMapObject->setNumber($playerObject->getNumber());
        $newMapObject->setMapObjectType(
            $this->repos()->getMapObjectTypeRepository()->find(MapObjectType::BOX)
        );

        $this->em()->persist($newMapObject);
        return true;
    }

    /**
     * Add Zenis on map
     *
     * @param Map $map Map
     * @param integer $zenis Zenis to add on the map
     */
    public function addZenisOnMap(Map $map, $zenis)
    {
        $numberItems = (int) ceil(($zenis * 0.5) / 30);
        $positions = $this->repos()->getMapRepository()->findPosition(
            $map,
            1,
            $map->getMaxX(),
            1,
            $map->getMaxY(),
            $numberItems
        );

        if (empty($positions)) {
            return;
        }

        if ($numberItems === 1) {
            $positions = [$positions];
        }


        foreach ($positions as $position) {
            $indexNumber = $numberItems-1;
            if (!empty($indexNumber)) {
                // We take a random amount of zenis in the stack
                $zenisAdded = mt_rand(1, ceil($zenis / $numberItems));
            } else {
                $zenisAdded = ceil($zenis);
            }

            $zenis -= $zenisAdded;
            $numberItems--;

            $mapObject = new MapObject();
            $mapObject->setMap($map);
            $mapObject->setX($position['x']);
            $mapObject->setY($position['y']);
            $mapObject->setMapObjectType(
                $this->repos()->getMapObjectTypeRepository()->findOneById(MapObjectType::ZENI)
            );
            $mapObject->setNumber($zenisAdded);

            $this->em()->persist($mapObject);
            $this->em()->flush();
        }
    }

    /**
     * Sell an item
     *
     * @param PlayerObject $playerObject Player object
     * @param integer $number NUmber of objects
     *
     * @return boolean
     */
    public function sell(PlayerObject $playerObject, $number = 0)
    {
        $number = (int) ($playerObject->getNumber() > $number ?
                         $playerObject->getNumber() :
                         $number);
        if ($number < 1) {
            return self::ERROR_SELL_NO_ITEM;
        }

        $zenis = ($playerObject->getObject()->getPrice() * 0.8) * $number;
        $playerObject->getPlayer()->setZeni($playerObject->getPlayer->getZeni() + $zenis);
        $playerObject->setNumber($playerObject->getNumber() - $number);

        return true;
    }
}
