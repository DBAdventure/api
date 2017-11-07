<?php

namespace Dba\GameBundle\Tests\Controller;

use Dba\GameBundle\Entity\Bank;

class BuildingControllerTest extends BaseTestCase
{
    const SHOP_ID = 2;
    const BANK_ID = 22;
    const TELEPORT_ID = 10;
    const WANTED_ID = 15;

    public function testEnterWithoutGoodPosition()
    {
        $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::SHOP_ID);

        $this->client->request('GET', '/api/building/enter/' . $building->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testEnterWithoutGoodMap()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::SHOP_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/api/building/enter/' . $building->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testEnterWithGoodMap()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::SHOP_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/api/building/enter/' . $building->getId());
        $this->assertJsonResponse($this->client->getResponse(), 200);
    }

    public function testEnterWithTeleport()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::TELEPORT_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/api/building/enter/' . $building->getId());
        $this->assertJsonResponse($this->client->getResponse(), 200);
    }

    public function testEnterWithBank()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::BANK_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/api/building/enter/' . $building->getId());
        $this->assertJsonResponse($this->client->getResponse(), 200);
    }

    public function testEnterWithWanted()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::WANTED_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/api/building/enter/' . $building->getId());
        $this->assertJsonResponse($this->client->getResponse(), 200);
    }

    public function testDepositBankWithNotABankBuilding()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::TELEPORT_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());

        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/bank/' . $building->getId() . '/deposit'
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testDepositBankWithNegativeZenis()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::BANK_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());

        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/bank/' . $building->getId() . '/deposit',
            [
                'deposit' => -100
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 400);
    }

    public function testDepositBankWithNotEnoughZenis()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::BANK_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());

        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/bank/' . $building->getId() . '/deposit',
            [
                'deposit' => 4000
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 400);
    }

    public function testDepositBank()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::BANK_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $player->setZeni(100);

        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/bank/' . $building->getId() . '/deposit',
            [
                'deposit' => 29
            ]
        );

        $this->assertJsonResponse($this->client->getResponse());
        $this->assertEquals(80, $player->getZeni());
        $bankPlayer = $this->repos()->getBankRepository()->findOneByPlayer($player);
        $this->assertEquals(1, $bankPlayer->getZeni());
    }

    public function testWithdrawBankWithNotABankBuilding()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::TELEPORT_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());

        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/bank/' . $building->getId() . '/withdraw'
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testWithdrawBankWithNotEnoughZenis()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::BANK_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());

        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/bank/' . $building->getId() . '/withdraw',
            [
                'withdraw' => 4000
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 400);
    }

    public function testWithdrawBankWithNegativeZenis()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::BANK_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $player->setZeni(100);

        $bankPlayer = new Bank();
        $bankPlayer->setPlayer($player);
        $bankPlayer->setZeni(10);
        $this->em()->persist($player);
        $this->em()->persist($bankPlayer);
        $this->em()->flush();
        $this->em()->refresh($bankPlayer);

        $this->client->request(
            'POST',
            '/api/building/bank/' . $building->getId() . '/withdraw',
            [
                'withdraw' => -9
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 400);
    }

    public function testWithdrawBank()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::BANK_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $player->setZeni(100);

        $bankPlayer = new Bank();
        $bankPlayer->setPlayer($player);
        $bankPlayer->setZeni(10);
        $this->em()->persist($player);
        $this->em()->persist($bankPlayer);
        $this->em()->flush();
        $this->em()->refresh($bankPlayer);

        $this->client->request(
            'POST',
            '/api/building/bank/' . $building->getId() . '/withdraw',
            [
                'withdraw' => 9
            ]
        );

        $this->assertJsonResponse($this->client->getResponse());
        $this->assertEquals(262, $player->getZeni());
        $this->assertEquals(1, $bankPlayer->getZeni());
        $bankPlayer = $this->repos()->getBankRepository()->findOneByPlayer($player);
        $this->assertEquals(1, $bankPlayer->getZeni());
    }

    public function testTeleportWithBadPosition()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::TELEPORT_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/teleport/' . $building->getId(),
            [
                'where' => 'new',
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testTeleportWithoutActionPoints()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::TELEPORT_ID);
        $player->setActionPoints(0);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/teleport/' . $building->getId(),
            [
                'where' => 'nw',
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testTeleportWithForbiddenTeleport()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::TELEPORT_ID);
        $player->setForbiddenTeleport('nw');
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/teleport/' . $building->getId(),
            [
                'where' => 'nw',
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testTeleport()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::TELEPORT_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $player->setActionPoints(20);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/teleport/' . $building->getId(),
            [
                'where' => 'ne',
            ]
        );
        $this->assertJsonResponse($this->client->getResponse());

        $this->assertNotEquals($building->getMap()->getId(), $player->getMap()->getId());
        $this->assertNotEquals($building->getY(), $player->getY());
        $this->assertNotEquals($building->getX(), $player->getX());
    }

    public function testBuyWithBadPosition()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::TELEPORT_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/shop/' . $building->getId() . '/object/2'
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testBuyItemWithWrongObject()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::SHOP_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/shop/' . $building->getId() . '/object/14'
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testBuyItemWithNumericReturn()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::SHOP_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $player->setZeni(2);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/shop/' . $building->getId() . '/object/2'
        );
        $json = $this->assertJsonResponse($this->client->getResponse(), 400);
        $this->assertEquals(2, $player->getZeni());
        $this->assertEquals(
            'building.shop.error.2',
            $json->error
        );
    }

    public function testBuyItem()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::SHOP_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/shop/' . $building->getId() . '/object/2'
        );
        $json = $this->assertJsonResponse($this->client->getResponse(), 200);
        $this->assertEquals(5, $player->getZeni());
        $this->assertEquals(
            'building.shop.success',
            $json->message
        );
        $this->assertEquals(
            'objects.senzu.name',
            $json->parameters->name
        );
    }

    public function testWantedWithBadPosition()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::SHOP_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/wanted/' . $building->getId()
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testWantedItemWithoutMoney()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::WANTED_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->createPlayer('bast');
        $this->client->request(
            'POST',
            '/api/building/wanted/' . $building->getId(),
            [
                'target' => 'bast',
            ]
        );
        $json = $this->assertJsonResponse($this->client->getResponse(), 400);
        $this->assertEquals('building.wanted.error', $json->error);
    }

    public function testWantedItemWithoutTarget()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::WANTED_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/building/wanted/' . $building->getId(),
            ['amount' => 20]
        );
        $json = $this->assertJsonResponse($this->client->getResponse(), 400);
        $this->assertEquals('building.wanted.error', $json->error);
    }

    public function testWantedItemWithNotEnoughMoney()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::WANTED_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $player->setZeni(60);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->createPlayer('bast');
        $this->client->request(
            'POST',
            '/api/building/wanted/' . $building->getId(),
            ['amount' => 70, 'target' => 'bast']
        );
        $json = $this->assertJsonResponse($this->client->getResponse(), 400);
        $this->assertEquals('building.wanted.error', $json->error);
    }

    public function testWanted()
    {
        $player = $this->login();
        $building = $this->repos()->getBuildingRepository()->findOneById(self::WANTED_ID);
        $player->setX($building->getX());
        $player->setY($building->getY());
        $player->setMap($building->getMap());
        $player->setZeni(60);
        $this->em()->persist($player);
        $this->em()->flush();

        $enemy = $this->createPlayer('bast');
        $this->client->request(
            'POST',
            '/api/building/wanted/' . $building->getId(),
            ['amount' => 55, 'target' => 'bast']
        );
        $json = $this->assertJsonResponse($this->client->getResponse());

        $this->assertEquals('building.wanted.zeni', $json->message);
        $this->assertEquals(55, $enemy->getHeadPrice());
        $this->assertEquals(5, $player->getZeni());
    }
}
