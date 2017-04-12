<?php

namespace Dba\GameBundle\Tests\Controller;

use Dba\GameBundle\Entity\PlayerObject;

class MapControllerTest extends BaseTestCase
{
    public function testRefreshMenu()
    {
        $this->login();
        $this->client->request('GET', '/map/refresh/menu');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testMinimapPng()
    {
        $this->login();
        $this->client->request('GET', '/map/mini.png');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testMiniMap()
    {
        $this->login();
        $this->client->request('GET', '/map/mini');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testMap()
    {
        $this->login();
        $this->client->request('GET', '/map');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testMapPartial()
    {
        $this->login();
        $this->client->request('GET', '/map/partial');
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->assertNotEmpty($json->content);
    }

    public function testElements()
    {
        $this->login();
        $this->client->request('GET', '/map/elements');
        $json = $this->assertJsonResponse($this->client->getResponse());
        $this->assertNotEmpty($json->content);
    }
}
