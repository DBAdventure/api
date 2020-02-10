<?php

namespace Dba\GameBundle\Tests\Controller;

class GuildControllerTest extends BaseTestCase
{
    public function testCreateGuildWihtouZeni()
    {
        $this->login();
        $this->client->request('POST', '/api/guild/create');
        $this->assertJsonResponse($this->client->getResponse(), 403);
    }

    public function testCreateGuildWithInvalidData()
    {
        $player = $this->login();
        $player->setZeni(250);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/guild/create',
            [
                'guild_create' => [
                    'name' => '',
                    'shortName' => 'DBA',
                    'history' => 'This is sparta!',
                ],
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 400);
    }

    public function testCreateGuild()
    {
        $player = $this->login();
        $player->setZeni(250);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request(
            'POST',
            '/api/guild/create',
            [
                'guild_create' => [
                    'name' => 'DBAdventure guild',
                    'shortName' => 'DBA',
                    'history' => 'This is sparta!',
                ],
            ]
        );
        $this->assertJsonResponse($this->client->getResponse());

        $player = $this->repos()->getPlayerRepository()->findOneByid($player->getId());
        $this->assertEquals(50, $player->getZeni());
    }
}
