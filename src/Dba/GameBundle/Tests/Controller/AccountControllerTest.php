<?php

namespace Dba\GameBundle\Tests\Controller;

class AccountControllerTest extends BaseTestCase
{
    public function testProfile()
    {
        $this->login();
        $this->client->request('GET', '/account/profile');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testDashboard()
    {
        $this->login();
        $this->client->request('GET', '/account/dashboard');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAppearance()
    {
        $this->login();
        $this->client->request('GET', '/account/appearance');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testAppearanceChange()
    {
        $player = $this->login();
        $crawler = $this->client->request('GET', '/account/appearance');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('#player_appearance_type'));
        $this->assertCount(1, $crawler->filter('#player_appearance_image'));
        $this->assertCount(1, $crawler->filter('#player_registration_race'));

        $form = $crawler->filter('#appearance-form')->form();
        $form->disableValidation();
        // set some values
        $form['player_appearance[type]'] = 'HS1';
        $form['player_appearance[image]'] = 'HS10.png';

        // submit the form
        $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(
            [],
            $session->getBag('flashes')->all()
        );

        $player = $this->repos()->getPlayerRepository()->findOneById($player->getId());
        $this->assertEquals('HS10.png', $player->getImage());
    }

    public function testAppearanceNotChange()
    {
        $player = $this->login();
        $crawler = $this->client->request('GET', '/account/appearance');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('#player_appearance_type'));
        $this->assertCount(1, $crawler->filter('#player_appearance_image'));
        $this->assertCount(1, $crawler->filter('#player_registration_race'));

        $form = $crawler->filter('#appearance-form')->form();
        $form->disableValidation();
        // set some values
        $form['player_appearance[type]'] = 'H1';

        // submit the form
        $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(
            [
                'danger' => [
                    'account.appearance.failed'
                ]
            ],
            $session->getBag('flashes')->all()
        );

        $player = $this->repos()->getPlayerRepository()->findOneById($player->getId());
        $this->assertEquals('HS15.png', $player->getImage());
    }

    public function testTrainingRoom()
    {
        $this->login();
        $this->client->request('GET', '/account/training/room');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testTrainingRoomWithWrongParameter()
    {
        $this->login();
        $this->client->request('GET', '/account/training/room/Nothing');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testTrainingRoomWithoutActionPoints()
    {
        $player = $this->login();
        $player->setActionPoints(0);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/account/training/room/health');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/account/training/room'),
            'response is a redirect to /account/training/room'
        );
    }

    public function testTrainingRoomWithoutSkillPoints()
    {
        $player = $this->login();
        $player->setSkillPoints(0);
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/account/training/room/health');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/account/training/room'),
            'response is a redirect to /account/training/room'
        );
    }

    public function testTrainingRoomHealth()
    {
        $player = $this->login();
        $player->setActionPoints(5);
        $player->setSkillPoints(1);
        $oldHealth = $player->getMaxHealth();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/account/training/room/health');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/account/training/room'),
            'response is a redirect to /account/training/room'
        );
        $this->assertEquals($oldHealth + 45, $player->getMaxHealth());
        $this->assertEquals($oldHealth + 45, $player->getHealth());
        $this->assertEquals(0, $player->getSkillPoints());
        $this->assertEquals(0, $player->getActionPoints());
    }

    public function testTrainingRoomKi()
    {
        $player = $this->login();
        $player->setActionPoints(5);
        $player->setSkillPoints(1);
        $oldKi = $player->getTotalMaxKi();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/account/training/room/ki');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/account/training/room'),
            'response is a redirect to /account/training/room'
        );

        $this->assertEquals($oldKi + 2, $player->getMaxKi());
        $this->assertEquals($oldKi + 2, $player->getTotalMaxKi());
        $this->assertEquals($oldKi + 2, $player->getKi());
        $this->assertEquals(0, $player->getSkillPoints());
        $this->assertEquals(0, $player->getActionPoints());
    }

    public function testTrainingRoomSkill()
    {
        $player = $this->login();
        $player->setActionPoints(5);
        $player->setSkillPoints(1);
        $oldResistance = $player->getResistance();
        $this->em()->persist($player);
        $this->em()->flush();

        $this->client->request('GET', '/account/training/room/resistance');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/account/training/room'),
            'response is a redirect to /account/training/room'
        );

        $this->assertEquals($oldResistance + 1, $player->getResistance());
        $this->assertEquals(0, $player->getSkillPoints());
        $this->assertEquals(0, $player->getActionPoints());
    }
}
