<?php

namespace Dba\GameBundle\Tests\Controller;

class MiscellaneousControllerTest extends BaseTestCase
{
    public function testFaq()
    {
        $this->client->request('GET', '/faq');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testContact()
    {
        $this->client->request('GET', '/contact');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testTeam()
    {
        $this->client->request('GET', '/team');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRules()
    {
        $this->client->request('GET', '/rules');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testHistory()
    {
        $this->client->request('GET', '/history');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testPlayerInfo()
    {
        $player = $this->createPlayer('test');
        $this->client->request('GET', '/player/' . $player->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testPlayerInfoWithWrongId()
    {
        $this->client->request('GET', '/player/0');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testRanking()
    {
        $this->client->request('GET', '/ranking');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRankingWithAnOtherPage()
    {
        $this->client->request('GET', '/ranking?page=1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRankingWithoutGuildType()
    {
        $this->login();
        $this->client->request('GET', '/ranking?type=guild');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRankingWithData()
    {
        $this->client->request('GET', '/ranking/slap-taken');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testRankingWithPlayerSearch()
    {
        $this->createPlayer('test');
        $this->createPlayer('bast');
        $this->createPlayer('got');
        $player = $this->login();
        $this->client->request('GET', '/ranking/slap-taken?who=' . $player->getName());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
