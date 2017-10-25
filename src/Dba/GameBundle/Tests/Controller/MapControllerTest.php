<?php

namespace Dba\GameBundle\Tests\Controller;

use Dba\GameBundle\Entity\PlayerObject;

class MapControllerTest extends BaseTestCase
{
    public function testMinimapPngWithoutMap()
    {
        $this->login();
        $this->client->request('GET', '/api/map/mini.png');
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testMinimapPng()
    {
        $player = $this->login();
        $playerObject = new PlayerObject();
        $playerObject->setObject($this->repos()->getObjectRepository()->findOneById(1));
        $playerObject->setPlayer($player);
        $playerObject->setNumber(1);
        $playerObject->setEquipped(false);
        $player->addPlayerObject($playerObject);
        $this->em()->persist($playerObject);
        $this->em()->flush();

        $this->client->request('GET', '/api/map/mini.png');
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('image/png', $response->headers->get('Content-Type'));
    }

    public function testMap()
    {
        $this->login();
        $this->client->request('GET', '/api/map/');
        $this->assertJsonResponse($this->client->getResponse());
    }
}
