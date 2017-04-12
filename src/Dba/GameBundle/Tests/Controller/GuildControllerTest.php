<?php

namespace Dba\GameBundle\Tests\Controller;

class GuildControllerTest extends BaseTestCase
{
    public function testIndexWithoutGuild()
    {
        $this->login();
        $this->client->request('GET', '/guild');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testIndexWithGuild()
    {
        $this->login();
        $this->client->request('GET', '/guild');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateGuildWihtouZeni()
    {
        $this->login();
        $this->client->request('GET', '/guild/create');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateGuild()
    {
        $player = $this->login();
        $player->setZeni(250);
        $this->em()->persist($player);
        $this->em()->flush();

        $crawler = $this->client->request('GET', '/guild/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->filter('#guild-create-form')->form();
        $form->disableValidation();
        // set some values
        $form['guild_create[name]'] = 'Dbadventure guild';
        $form['guild_create[shortName]'] = 'DBA';
        $form['guild_create[history]'] = 'This is sparta!';
        $crawler = $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'Guild has been created, but you must wait for a validation by an administrator.'
                ]
            ],
            $session->getBag('flashes')->all()
        );
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/guild'),
            'response is a redirect to /guild'
        );

        $player = $this->repos()->getPlayerRepository()->findOneByid($player->getId());
        $this->assertEquals(50, $player->getZeni());
    }
}
