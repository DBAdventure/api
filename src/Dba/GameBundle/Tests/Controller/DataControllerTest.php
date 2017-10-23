<?php

namespace Dba\GameBundle\Tests\Controller;

class DataControllerTest extends BaseTestCase
{
    public function testGameInfo()
    {
        $this->client->request('GET', '/api/data/game');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testAppearance()
    {
        $this->client->request('GET', '/api/data/appearance');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testPlayerInfo()
    {
        $player = $this->createPlayer('test');
        $this->client->request('GET', '/api/data/player/' . $player->getId());
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testPlayerInfoWithWrongId()
    {
        $this->client->request('GET', '/api/data/player/0');
        $this->assertJsonResponse($this->client->getResponse(), 404);
    }

    public function testRanking()
    {
        $this->client->request('GET', '/api/data/ranking');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testRankingWithAnOtherPage()
    {
        $this->client->request('GET', '/api/data/ranking?page=1');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testRankingWithoutGuildType()
    {
        $this->login();
        $this->client->request('GET', '/api/data/ranking?type=guild');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testRankingWithData()
    {
        $this->client->request('GET', '/api/data/ranking/slap-taken');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testRankingWithPlayerSearch()
    {
        $this->createPlayer('test');
        $this->createPlayer('bast');
        $this->createPlayer('got');
        $player = $this->login();
        $this->client->request('GET', '/api/data/ranking/slap-taken?who=' . $player->getName());
        $this->assertJsonResponse($this->client->getResponse());
    }
}
