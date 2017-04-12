<?php

namespace Dba\GameBundle\Tests\Controller;

use Dba\GameBundle\Entity\PlayerObject;

class InventoryControllerTest extends BaseTestCase
{
    public function testInventoryWithItems()
    {
        $this->createItems();
        $this->client->request('GET', '/inventory');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testUseItemThatCantBeUsed()
    {
        $this->createItems();
        $this->client->request('GET', '/inventory/use/1');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'danger' => [
                    'You can\'t use this object.'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testUseItemWithHealthPercent()
    {
        $this->createItems();
        $player = $this->login();
        $player->setHealth(25);
        $player->setMaxHealth(100);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/inventory/use/3');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(75, $player->getHealth());
        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'You used 1 Potion of life!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testUseItemWithTeleport()
    {
        $this->createItems();
        $player = $this->login();
        $x = $player->getX();
        $y = $player->getY();
        $mapId = $player->getMap()->getId();

        $this->client->request('GET', '/inventory/use/4');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->assertNotEquals($x, $player->getX());
        $this->assertNotEquals($y, $player->getY());
        $this->assertNotEquals($mapId, $player->getMap()->getId());
        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'You used 1 Teleport cloud n°1!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
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

        $this->client->request('GET', '/inventory/use/4');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->assertEquals($x, $player->getX());
        $this->assertEquals($y, $player->getY());
        $this->assertEquals($mapId, $player->getMap()->getId());
        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'danger' => [
                    'Not enough movement points'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testUseItemWithHealthPoints()
    {
        $this->createItems();
        $player = $this->login();
        $player->setHealth(50);
        $player->setMaxHealth(100);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/inventory/use/27');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());


        $this->assertEquals(80, $player->getHealth());
        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'You used 1 Wild berries!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testDropItem()
    {
        $this->createItems();
        $player = $this->login();

        $this->client->request('GET', '/inventory/drop/12');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $player,
                'object' => $this->repos()->getObjectRepository()->findOneById(12)
            ]
        );

        $this->assertEquals(0, $playerObject->getNumber());
        $this->assertFalse($playerObject->getEquipped());

        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'Sayajin detector has been drop on the map!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testDropItemCantBeDropped()
    {
        $this->createItems();
        $this->login();
        $this->client->request('GET', '/inventory/drop/35');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'danger' => [
                    'You can\'t drop this object.'
                ]
            ],
            $session->getBag('flashes')->all()
        );
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
        $this->client->request('GET', '/inventory/equip/12');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->em()->refresh($playerObject);

        $this->assertFalse($playerObject->getEquipped());

        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'Antennas of King Kai has been unequipped!',
                    'Sayajin detector has been equipped!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testEquipCantEquipItemDueToNotFoundItem()
    {
        $this->createItems();
        $this->client->request('GET', '/inventory/equip/50');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEquipCantEquipItem()
    {
        $this->createItems();
        $this->client->request('GET', '/inventory/equip/2');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'danger' => [
                    'You can\'t equip this object.'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testEquipCanEquipItemDueToCapacity()
    {
        $player = $this->login();
        $player->setAgility(100);
        $this->createItems();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/inventory/equip/45');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'Bottes de Broly has been equipped!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testEquipCantEquipItemDueToCapacity()
    {
        $this->createItems();
        $this->client->request('GET', '/inventory/equip/45');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'danger' => [
                    'Bottes de Broly can not be equipped right now!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
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

        $this->client->request('GET', '/inventory/unequip/12');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->em()->refresh($playerObject);

        $this->assertFalse($playerObject->getEquipped());

        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'Sayajin detector has been unequipped!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
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
