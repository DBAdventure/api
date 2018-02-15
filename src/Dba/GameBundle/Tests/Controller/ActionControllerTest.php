<?php

namespace Dba\GameBundle\Tests\Controller;

use Dba\GameBundle\Entity\MapObjectType;
use Dba\GameBundle\Entity\MapObject;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\PlayerObject;
use Dba\GameBundle\Entity\Map;
use Dba\GameBundle\Entity\Side;

class ActionControllerTest extends BaseTestCase
{
    public function testConvertCantActionPoints()
    {
        $player = $this->login();
        $player->setActionPoints(0);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/convert');
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testConvertCantMovementPoints()
    {
        $player = $this->login();
        $player->setMovementPoints(Player::MAX_MOVEMENT_POINTS);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/convert');
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testConvert()
    {
        $player = $this->login();
        $player->setActionPoints(20);
        $player->setMovementPoints(20);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/convert');
        $this->assertJsonResponse($this->client->getResponse(), 200);
        $this->em()->refresh($player);
        $this->assertEquals(0, $player->getActionPoints());
        $this->assertEquals(60, $player->getMovementPoints());
    }

    public function testMoveWrongPosition()
    {
        $this->login();
        $this->client->request('POST', '/api/action/move/nowhere');
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testMoveWithoutMovementPoints()
    {
        $player = $this->login();
        $player->setMovementPoints(3);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/move/n');
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testMoveWithInvalidPosition()
    {
        $player = $this->login();
        $player->setX(1);
        $player->setY(1);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/move/n');
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testMoveOnRespawn()
    {
        $player = $this->login();
        $player->setX(10);
        $player->setY(10);
        $player->setMovementPoints(10);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/move/n');
        $this->assertJsonResponse($this->client->getResponse());

        $this->em()->refresh($player);
        $this->assertEquals(10, $player->getX());
        $this->assertEquals(9, $player->getY());
        $this->assertEquals(10, $player->getMovementPoints());
    }

    public function testMoveNorth()
    {
        $this->assertTestPosition('n', 10, 9, 5);
    }

    public function testMoveNorthEast()
    {
        $this->assertTestPosition('ne', 11, 9, 6);
    }

    public function testMoveNorthWest()
    {
        $this->assertTestPosition('nw', 9, 9, 6);
    }

    public function testMoveSouth()
    {
        $this->assertTestPosition('s', 10, 11, 5);
    }

    public function testMoveSouthEast()
    {
        $this->assertTestPosition('se', 11, 11, 6);
    }

    public function testMoveSouthWest()
    {
        $this->assertTestPosition('sw', 9, 11, 6);
    }

    public function testMoveEast()
    {
        $this->assertTestPosition('e', 11, 10, 5);
    }

    public function testMoveWest()
    {
        $this->assertTestPosition('w', 9, 10, 5);
    }

    public function testPickupWrongXPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject = $this->createMapObject();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testPickupWrongYPosition()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject = $this->createMapObject();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testPickupWrongMapPosition()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(50);
        $player->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $mapObject = $this->createMapObject();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testPickupZeni()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(50);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $player->setZeni(50);
        $mapObject = $this->createMapObject();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $this->assertEquals(100, $player->getZeni());
    }

    public function testPickupBush()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(50);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject = $this->createMapObject(MapObjectType::BUSH);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $this->assertEquals(50, $player->getZeni());
        $this->assertGreaterThan(0, count($player->getPlayerObjects()));
    }

    public function testPickupCapsuleWithDamagesAndDeath()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(50);
        $player->setHealth(1);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject = $this->createMapObject(MapObjectType::CAPSULE_BLUE, 30);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $this->assertNotEquals(50, $player->getX());
        $this->assertNotEquals(50, $player->getY());
    }

    public function testPickupCapsuleWithObjectPear()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(50);
        $player->setHealth(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject = $this->createMapObject(MapObjectType::CAPSULE_BLUE, 80);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $playerObject = $player->getPlayerObjects()[0];
        $this->assertEquals(5, $playerObject->getNumber());
        $this->assertEquals(
            Object::DEFAULT_PEAR,
            $playerObject->getObject()->getId()
        );
    }

    public function testPickupCapsuleWithObjectPotionOfLife()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(50);
        $player->setHealth(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject = $this->createMapObject(MapObjectType::CAPSULE_BLUE, 90);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $playerObject = $player->getPlayerObjects()[0];
        $this->assertEquals(2, $playerObject->getNumber());
        $this->assertEquals(
            Object::DEFAULT_POTION_OF_LIFE,
            $playerObject->getObject()->getId()
        );
    }

    public function testPickupCapsuleWithObjectSenzu()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(50);
        $player->setHealth(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject = $this->createMapObject(MapObjectType::CAPSULE_BLUE, 100);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $playerObject = $player->getPlayerObjects()[0];
        $this->assertEquals(1, $playerObject->getNumber());
        $this->assertEquals(
            Object::DEFAULT_SENZU,
            $playerObject->getObject()->getId()
        );
    }

    public function testPickupSign()
    {
        $player = $this->login();
        $player->setX(50);
        $player->setY(50);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject = $this->createMapObject(MapObjectType::SIGN);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/pickup/' . $mapObject->getId());
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->assertEquals($json->messages[0], 'Bast');
        $this->assertEquals($json->messages[1], 'GoT');
        $this->em()->refresh($player);
    }
    public function testAttackDifferentXPosition()
    {
        $player = $this->login();
        $player->setX(1);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(2);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAttackDifferentYPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(10);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAttackDifferentMapPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAttackWithoutActionPoints()
    {
        $player = $this->login();
        $player->setActionPoints(1);
        $enemy = $this->createPlayer('bast');
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAttackAnOtherPlayer()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());

        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());

        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        for ($i = 0; $i <= 10; $i++) {
            $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
            if ($this->client->getResponse()->getStatusCode() != 200 && $i >= 5) {
                return;
            }

            $this->assertJsonResponse($this->client->getResponse(), 200);
        }
    }

    public function testAttackWithoutGoodMap()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAttackALowerLevel()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setLevel(50);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();
        $oldHealth = $player->getHealth();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $this->assertEquals($oldHealth - 50, $player->getHealth());
    }

    public function testAttackAnOtherPlayerBetray()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId() . '/betray');
        $this->assertJsonResponse($this->client->getResponse());

        $this->em()->refresh($enemy);
        $this->assertEquals($player->getId(), $enemy->getTarget()->getId());
    }

    public function testAttackAnOtherPlayerBetrayWithoutSameSide()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::BAD);
        $player->setSide($side);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::GOOD);
        $enemy->setSide($side);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId() . '/betray');
        $this->assertJsonResponse($this->client->getResponse());

        $this->em()->refresh($enemy);
        $this->assertNull($enemy->getTarget());
    }

    public function testAttackKillGood()
    {
        $player = $this->login();
        $player->setX(40);
        $player->setY(40);
        $player->setAccuracy(100);
        $player->setStrength(100);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::BAD);
        $player->setSide($side);

        $enemy = $this->createPlayer('bast');
        $enemy->setX(40);
        $enemy->setY(40);
        $enemy->setHealth(10);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::GOOD);
        $enemy->setSide($side);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());

        $this->em()->refresh($enemy);
        $this->assertNotEquals(
            $this->repos()->getMapRepository()->getDefaultMap(),
            $enemy->getMap()->getId()
        );
        $this->assertNull($enemy->getTarget());
    }

    public function testAttackKillGoodBetray()
    {
        $player = $this->login();
        $player->setX(40);
        $player->setY(40);
        $player->setAccuracy(100);
        $player->setStrength(100);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::GOOD);
        $player->setSide($side);

        $enemy = $this->createPlayer('bast');
        $enemy->setX(40);
        $enemy->setY(40);
        $enemy->setHealth(10);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy->setSide($side);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId() . '/betray');
        $this->assertJsonResponse($this->client->getResponse());

        $this->em()->refresh($player);
        $this->em()->refresh($enemy);
        $this->assertNotEquals(
            $this->repos()->getMapRepository()->getDefaultMap(),
            $enemy->getMap()->getId()
        );
        $this->assertEquals($player->getId(), $enemy->getTarget()->getId());
        $this->assertEquals(Side::BAD, $player->getSide()->getId());
    }

    public function testAttackKillBad()
    {
        $player = $this->login();
        $player->setX(40);
        $player->setY(40);
        $player->setAccuracy(100);
        $player->setStrength(100);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::GOOD);
        $player->setSide($side);

        $enemy = $this->createPlayer('bast');
        $enemy->setX(40);
        $enemy->setY(40);
        $enemy->setHealth(10);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::BAD);
        $enemy->setSide($side);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());

        $this->em()->refresh($enemy);
        $this->assertNotEquals(40, $enemy->getX());
        $this->assertNotEquals(40, $enemy->getY());
        $this->assertNotEquals(
            $this->repos()->getMapRepository()->getDefaultMap(),
            $enemy->getMap()->getId()
        );
        $this->assertNull($enemy->getTarget());
    }

    public function testAttackKillBadBetrayAndHeadPrice()
    {
        $player = $this->login();
        $player->setX(40);
        $player->setY(40);
        $player->setAccuracy(100);
        $player->setStrength(100);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::BAD);
        $player->setSide($side);
        $player->setZeni(10);

        $enemy = $this->createPlayer('bast');
        $enemy->setX(40);
        $enemy->setY(40);
        $enemy->setHealth(10);
        $enemy->setHeadPrice(100);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $side = $this->repos()->getSideRepository()->findOneById(Side::BAD);
        $enemy->setSide($side);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/attack/' . $enemy->getId() . '/betray');
        $this->assertJsonResponse($this->client->getResponse());

        $this->em()->refresh($player);
        $this->em()->refresh($enemy);
        $this->assertNotEquals(
            $this->repos()->getMapRepository()->getDefaultMap(),
            $enemy->getMap()->getId()
        );
        $this->assertEquals($player->getId(), $enemy->getTarget()->getId());
        $this->assertEquals(Side::GOOD, $player->getSide()->getId());
        $this->assertEquals(110, $player->getZeni());
        $this->assertEquals(0, $enemy->getHeadPrice());
        $this->assertEquals(1, $player->getNbWanted());
    }

    protected function assertTestPosition($where, $x, $y, $usage)
    {
        $player = $this->login();
        $player->setX(10);
        $player->setY(10);
        $player->setMovementPoints(10);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->flush();

        $this->client->request('POST', '/api/action/move/' . $where);
        $this->assertJsonResponse($this->client->getResponse());

        $this->em()->refresh($player);
        $this->assertEquals($x, $player->getX());
        $this->assertEquals($y, $player->getY());
        $this->assertEquals(10 - $usage, $player->getMovementPoints());
    }

    public function testStealDifferentXPosition()
    {
        $player = $this->login();
        $player->setX(1);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(2);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/steal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testStealDifferentYPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(1);
        $enemy->setY(10);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/steal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testStealDifferentMapPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/steal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testStealWithoutActionPoints()
    {
        $player = $this->login();
        $player->setActionPoints(1);
        $enemy = $this->createPlayer('bast');
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/steal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testStealWithoutGoodMap()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/steal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testStealAnOtherPlayer()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy->setZeni(10000);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        for ($i = 0; $i <= 10; $i++) {
            $this->client->request('POST', '/api/action/steal/' . $enemy->getId());
            $this->assertJsonResponse($this->client->getResponse());
        }
    }

    public function testStealALowerLevel()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setLevel(50);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();
        $oldHealth = $player->getHealth();

        $this->client->request('POST', '/api/action/steal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $this->assertEquals($oldHealth - 50, $player->getHealth());
    }

    public function testAnalysisDifferentXPosition()
    {
        $player = $this->login();
        $player->setX(1);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(2);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/analysis/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAnalysisDifferentYPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(1);
        $enemy->setY(10);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/analysis/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAnalysisDifferentMapPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/analysis/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAnalysisWithoutGoodMap()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $player->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/analysis/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }


    public function testAnalysisWithoutActionPoints()
    {
        $player = $this->login();
        $player->setActionPoints(1);
        $enemy = $this->createPlayer('bast');
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/analysis/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testAnalysisAnOtherPlayer()
    {
        $player = $this->login();
        $player->setActionPoints(6);
        $player->setBattlePoints(100);
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());

        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/analysis/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $this->em()->refresh($enemy);
        $this->assertEquals(23, $player->getActionPoints()); // He level up
        $this->assertNotEquals(100, $player->getBattlePoints());
    }

    public function testSlapDifferentXPosition()
    {
        $player = $this->login();
        $player->setX(1);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(2);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/analysis/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testSlapDifferentYPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(1);
        $enemy->setY(10);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/slap/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testSlapDifferentMapPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/slap/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testSlapWithoutBetrayals()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/slap/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testSlapAnOtherPlayer()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setActionPoints(10);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setBetrayals(10);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/slap/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $this->em()->refresh($enemy);
        $this->assertEquals(10, $player->getActionPoints());
        $this->assertEquals(1, $player->getNbSlapGiven());
        $this->assertEquals(9, $enemy->getBetrayals());
        $this->assertEquals(1, $enemy->getNbSlapTaken());
    }

    public function testSlapAnOtherPlayerAndHeDie()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setActionPoints(10);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setHealth(1);
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setBetrayals(10);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/slap/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($player);
        $this->em()->refresh($enemy);
        $this->assertEquals(10, $player->getActionPoints());
        $this->assertEquals(1, $player->getNbSlapGiven());
        $this->assertEquals(9, $enemy->getBetrayals());
        $this->assertEquals(1, $enemy->getNbSlapTaken());
        $this->assertNotEquals(
            $this->repos()->getMapRepository()->getDefaultMap(),
            $enemy->getMap()->getId()
        );
    }

    public function testGiveDifferentXPosition()
    {
        $player = $this->login();
        $player->setX(1);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(2);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/give/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testGiveDifferentYPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(10);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/give/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testGiveDifferentMapPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/give/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testGiveWithoutActionPoints()
    {
        $player = $this->login();
        $player->setActionPoints(1);
        $enemy = $this->createPlayer('bast');
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/give/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testGiveToOtherPlayer()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/give/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testGiveZeniToOtherPlayer()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $player->setZeni(100);
        $player->setActionPoints(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy->setZeni(100);

        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/action/give/' . $enemy->getId(),
            ['zenis' => 10]
        );
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($enemy);
        $this->em()->refresh($player);
        $this->assertEquals(90, $player->getZeni());
        $this->assertEquals(3, $player->getActionPoints());
        $this->assertEquals(110, $enemy->getZeni());
    }

    public function testGiveTonOfZeniToOtherPlayer()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $player->setZeni(100);
        $player->setActionPoints(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy->setZeni(100);

        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/action/give/' . $enemy->getId(),
            ['zenis' => 10000]
        );
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($enemy);
        $this->em()->refresh($player);
        $this->assertEquals(0, $player->getZeni());
        $this->assertEquals(3, $player->getActionPoints());
        $this->assertEquals(200, $enemy->getZeni());
    }

    public function testGiveObjectToOtherPlayer()
    {
        $this->createItems();
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $player->setZeni(100);
        $player->setActionPoints(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy->setZeni(100);

        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/action/give/' . $enemy->getId() . '/12',
            ['quantity' => 100]
        );
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($enemy);
        $this->em()->refresh($player);
        $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $player,
                'object' => $this->repos()->getObjectRepository()->findOneById(12)
            ]
        );
        $this->assertEquals(0, $playerObject->getNumber());
        $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $enemy,
                'object' => $this->repos()->getObjectRepository()->findOneById(12)
            ]
        );
        $this->assertEquals(1, $playerObject->getNumber());
        $this->assertEquals(3, $player->getActionPoints());
    }

    public function testGiveNotFoundObjectToOtherPlayer()
    {
        $this->createItems();
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $player->setZeni(5);
        $player->setActionPoints(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy->setZeni(5);
        $playerObject = $this->repos()->getPlayerObjectRepository()->findOneBy(
            [
                'player' => $player,
                'object' => $this->repos()->getObjectRepository()->findOneById(12)
            ]
        );
        $playerObject->setNumber(0);

        $this->em()->persist($enemy);
        $this->em()->persist($playerObject);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/action/give/' . $enemy->getId() . '/12',
            ['quantity' => 100]
        );
        $this->assertJsonResponse($this->client->getResponse(), 403);
        $this->em()->refresh($enemy);
        $this->em()->refresh($player);
        $this->assertEquals(5, $player->getActionPoints());
    }

    public function testHealDifferentXPosition()
    {
        $player = $this->login();
        $player->setX(1);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(2);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/heal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testHealDifferentYPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(10);
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/heal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testHealDifferentMapPosition()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->findOneById(Map::HEAVEN));
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/heal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testHealWithoutActionPoints()
    {
        $player = $this->login();
        $player->setActionPoints(1);
        $enemy = $this->createPlayer('bast');
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/heal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testHealWithHealthAlreadyFull()
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/heal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testHealAnOtherPlayer()
    {
        $this->runTestHealWithSkillPoints(15);
    }

    public function testHealAnOtherPlayerWith20Skill()
    {
        $this->runTestHealWithSkillPoints(20);
    }

    public function testHealAnOtherPlayerWith30Skill()
    {
        $this->runTestHealWithSkillPoints(30);
    }

    public function testHealAnOtherPlayerWith50Skill()
    {
        $this->runTestHealWithSkillPoints(50);
    }

    public function testHealAnOtherPlayerWith70Skill()
    {
        $this->runTestHealWithSkillPoints(70);
    }

    public function testHealAnOtherPlayerWith90Skill()
    {
        $this->runTestHealWithSkillPoints(90);
    }

    public function testHealAnOtherPlayerWith130Skill()
    {
        $this->runTestHealWithSkillPoints(130);
    }

    public function testHealAnOtherPlayerWith150Skill()
    {
        $this->runTestHealWithSkillPoints(150);
    }

    protected function runTestHealWithSkillPoints($skill)
    {
        $player = $this->login();
        $player->setX(5);
        $player->setY(5);
        $player->setSkill($skill);
        $player->setIntellect($skill);
        $player->setActionPoints(60);
        $player->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $enemy = $this->createPlayer('bast');
        $enemy->setX(5);
        $enemy->setY(5);
        $enemy->setHealth(10);
        $enemy->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $this->em()->persist($enemy);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('POST', '/api/action/heal/' . $enemy->getId());
        $this->assertJsonResponse($this->client->getResponse());
        $this->em()->refresh($enemy);
        $this->em()->refresh($player);
        if ($skill == 10) {
            $this->assertNotEquals(10, $enemy->getHealth());
        }
        $this->assertEquals(56, $player->getActionPoints());

        for ($i = 0; $i <= ($skill > 50 ? 2 : 10); $i++) {
            $this->client->request('POST', '/api/action/heal/' . $enemy->getId());
            $this->assertJsonResponse($this->client->getResponse());
        }
    }

    protected function createItems()
    {
        $player = $this->login();
        foreach (range(1, 12) as $objectId) {
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

    protected function createMapObject($what = MapObjectType::ZENI, $forceNumber = null)
    {
        $mapObject = new MapObject();
        $mapObject->setMap($this->repos()->getMapRepository()->getDefaultMap());
        $mapObject->setX(50);
        $mapObject->setY(50);
        $mapObject->setMapObjectType($this->repos()->getMapObjectTypeRepository()->findOneById($what));
        switch ($what) {
            case MapObjectType::BUSH:
                $mapObject->setNumber(mt_rand(1, 5));
                $mapObject->setObject(
                    $this->repos()->getObjectRepository()->findOneById(Object::DEFAULT_PEAR)
                );
                break;
            case MapObjectType::CAPSULE_BLUE:
            case MapObjectType::CAPSULE_RED:
                $mapObject->setNumber(mt_rand(1, 100));
                break;
            case MapObjectType::SIGN:
                $mapObject->setExtra([
                    ['key' => MapObject::EXTRA_DIALOGUE, 'value' => 'Bast'],
                    ['key' => MapObject::EXTRA_DIALOGUE, 'value' => 'GoT']
                ]);
                break;
            default:
                $mapObject->setNumber(50);
        }

        if ($forceNumber !== null) {
            $mapObject->setNumber($forceNumber);
        }
        $this->em()->persist($mapObject);
        $this->em()->flush();

        return $mapObject;
    }
}
