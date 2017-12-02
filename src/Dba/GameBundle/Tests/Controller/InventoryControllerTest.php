<?php

namespace Dba\GameBundle\Tests\Controller;

use Dba\GameBundle\Entity\PlayerObject;

class InventoryControllerTest extends BaseTestCase
{
    public function testInventoryWithItems()
    {
        $this->createItems();
        $this->client->request('GET', '/api/inventory/objects');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testUseItemThatCantBeUsed()
    {
        $this->createItems();
        $this->client->request('POST', '/api/inventory/use/1');
        $json = $this->assertJsonResponse($this->client->getResponse(), 403);
        $this->assertEquals('inventory.object.cant.use', $json->error);
    }

    public function testUseItemWithHealthPercent()
    {
        $this->createItems();
        $player = $this->login();
        $player->setHealth(25);
        $player->setMaxHealth(100);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/inventory/use/3');
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->assertEquals('inventory.object.used', $json->message);
        $this->assertEquals(1, $json->parameters->number);
        $this->assertEquals('Potion de vie', $json->parameters->name);

        $this->assertEquals(75, $player->getHealth());
    }

    public function testUseItemWithTeleport()
    {
        $this->createItems();
        $player = $this->login();
        $x = $player->getX();
        $y = $player->getY();
        $mapId = $player->getMap()->getId();

        $this->client->request('POST', '/api/inventory/use/4');
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->assertNotEquals($x, $player->getX());
        $this->assertNotEquals($y, $player->getY());
        $this->assertNotEquals($mapId, $player->getMap()->getId());
        $this->assertEquals('inventory.object.used', $json->message);
        $this->assertEquals(1, $json->parameters->number);
        $this->assertEquals('Nuage magique n°1', $json->parameters->name);
    }

    public function testUseItemWithTeleportWithoutMovementPoints()
    {
        $this->createItems();
        $player = $this->login();
        $x = $player->getX();
        $y = $player->getY();
        $mapId = $player->getMap()->getId();
        $player->setMovementPoints(0);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/inventory/use/4');
        $json = $this->assertJsonResponse($this->client->getResponse(), 403);

        $this->assertEquals($x, $player->getX());
        $this->assertEquals($y, $player->getY());
        $this->assertEquals($mapId, $player->getMap()->getId());
        $this->assertEquals('inventory.object.error.teleport', $json->error);
    }

    public function testUseItemWithHealthPoints()
    {
        $this->createItems();
        $player = $this->login();
        $player->setHealth(50);
        $player->setMaxHealth(100);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/inventory/use/27');
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->assertEquals(80, $player->getHealth());
        $this->assertEquals('inventory.object.used', $json->message);
        $this->assertEquals(1, $json->parameters->number);
        $this->assertEquals('Baies sauvages', $json->parameters->name);
    }

    public function testDropItem()
    {
        $this->createItems();
        $player = $this->login();

        $this->client->request('POST', '/api/inventory/drop/12');
        $json = $this->assertJsonResponse($this->client->getResponse());

        $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $player,
                'object' => $this->repos()->getObjectRepository()->findOneById(12)
            ]
        );

        $this->assertEquals(0, $playerObject->getNumber());
        $this->assertFalse($playerObject->getEquipped());
        $this->assertEquals('inventory.object.drop', $json->message);
        $this->assertEquals('Détecteur Saïyen', $json->parameters->name);
    }

    public function testDropItemCantBeDropped()
    {
        $this->createItems();
        $this->login();
        $this->client->request('POST', '/api/inventory/drop/35');
        $json = $this->assertJsonResponse($this->client->getResponse(), 403);
        $this->assertEquals('inventory.object.cant.drop', $json->error);
    }

    public function testEquipItem()
    {
        $this->createItems();
        $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $this->login(),
                'object' => $this->repos()->getObjectRepository()->findOneById(16)
            ]
        );
        $playerObject->setEquipped(true);
        $this->em()->persist($playerObject);
        $this->em()->flush();
        $this->client->request('POST', '/api/inventory/equip/12');
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($playerObject);

        $this->assertFalse($playerObject->getEquipped());
        $this->assertEquals('inventory.object.unequip', $json->messages[0]->message);
        $this->assertEquals('Antennes du roi Kaïo', $json->messages[0]->parameters->name);
        $this->assertEquals('inventory.object.equip', $json->messages[1]->message);
        $this->assertEquals('Détecteur Saïyen', $json->messages[1]->parameters->name);
    }

    public function testEquipCantEquipItemDueToNotFoundItem()
    {
        $this->createItems();
        $this->client->request('POST', '/api/inventory/equip/50');
        $this->assertJsonResponse($this->client->getResponse(), 404);
    }

    public function testEquipCantEquipItem()
    {
        $this->createItems();
        $this->client->request('POST', '/api/inventory/equip/2');
        $json = $this->assertJsonResponse($this->client->getResponse(), 403);
        $this->assertEquals('inventory.object.cant.equip', $json->error);
    }

    public function testEquipCanEquipItemDueToCapacity()
    {
        $player = $this->login();
        $player->setAgility(100);
        $this->createItems();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/inventory/equip/45');
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->assertEquals('inventory.object.equip', $json->messages[0]->message);
        $this->assertEquals('Bottes de Broly', $json->messages[0]->parameters->name);
    }

    public function testEquipCantEquipItemDueToCapacity()
    {
        $this->createItems();
        $this->client->request('POST', '/api/inventory/equip/45');
        $json = $this->assertJsonResponse($this->client->getResponse(), 403);
        $this->assertEquals('inventory.object.error.equip', $json->error->message);
        $this->assertEquals('Bottes de Broly', $json->error->parameters->name);
    }

    public function testUnequip()
    {
        $this->createItems();
        $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $this->login(),
                'object' => $this->repos()->getObjectRepository()->findOneById(12)
            ]
        );
        $playerObject->setEquipped(true);
        $this->em()->persist($playerObject);
        $this->em()->flush();

        $this->client->request('POST', '/api/inventory/unequip/12');
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($playerObject);

        $this->assertFalse($playerObject->getEquipped());
        $this->assertEquals('inventory.object.unequip', $json->message);
        $this->assertEquals('Détecteur Saïyen', $json->parameters->name);
    }

    protected function createItems()
    {
        $player = $this->login();
        foreach (range(1, 45) as $objectId) {
            $playerObject = new PlayerObject();
            $playerObject->setObject($this->repos()->getObjectRepository()->findOneById($objectId));
            $playerObject->setPlayer($player);
            $playerObject->setNumber($objectId == 10 ? 0 : 1);
            $playerObject->setEquipped(false);
            $player->addPlayerObject($playerObject);
            $this->em()->persist($playerObject);
        }
        $this->em()->flush();
    }
}
