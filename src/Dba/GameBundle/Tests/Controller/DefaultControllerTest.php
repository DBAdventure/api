<?php

namespace Dba\GameBundle\Tests\Controller;

class DefaultControllerTest extends BaseTestCase
{
    public function testHome()
    {
        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testConfirm()
    {
        $this->registerClass(1);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->client->request(
            'GET',
            '/confirm/' . $player->getId() . '/' . $player->getConfirmationToken()
        );
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $session = $this->container->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'account.created',
                    'account.enabled'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    /**
     * @ExpectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testConfirmWithWrongId()
    {
        $this->registerClass(1);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->client->request('GET', '/confirm/99999/' . $player->getConfirmationToken());
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }


    /**
     * @ExpectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testConfirmWithWrongToken()
    {
        $this->client->request('GET', '/confirm/99999/badToken');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testRegisterClassOne()
    {
        $this->registerClass(1);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->assertEquals(770, $player->getHealth());
        $this->assertEquals(770, $player->getMaxHealth());
        $this->assertEquals(1, $player->getKi());
        $this->assertEquals(1, $player->getMaxKi());
        $this->assertEquals(10, $player->getStrength());
        $this->assertEquals(5, $player->getResistance());
        $this->assertEquals(9, $player->getAccuracy());
        $this->assertEquals(3, $player->getAgility());
        $this->assertEquals(1, $player->getVision());
        $this->assertEquals(1, $player->getAnalysis());
        $this->assertEquals(1, $player->getSkill());
        $this->assertEquals(1, $player->getIntellect());
    }

    public function testRegisterClassTwo()
    {
        $this->registerClass(2);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->assertEquals(365, $player->getHealth());
        $this->assertEquals(365, $player->getMaxHealth());
        $this->assertEquals(15, $player->getKi());
        $this->assertEquals(15, $player->getMaxKi());
        $this->assertEquals(1, $player->getStrength());
        $this->assertEquals(3, $player->getResistance());
        $this->assertEquals(1, $player->getAccuracy());
        $this->assertEquals(1, $player->getAgility());
        $this->assertEquals(5, $player->getVision());
        $this->assertEquals(3, $player->getAnalysis());
        $this->assertEquals(4, $player->getSkill());
        $this->assertEquals(15, $player->getIntellect());
    }

    public function testRegisterClassThree()
    {
        $this->registerClass(3);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->assertEquals(500, $player->getHealth());
        $this->assertEquals(500, $player->getMaxHealth());
        $this->assertEquals(1, $player->getKi());
        $this->assertEquals(1, $player->getMaxKi());
        $this->assertEquals(5, $player->getStrength());
        $this->assertEquals(1, $player->getResistance());
        $this->assertEquals(11, $player->getAccuracy());
        $this->assertEquals(15, $player->getAgility());
        $this->assertEquals(1, $player->getVision());
        $this->assertEquals(2, $player->getAnalysis());
        $this->assertEquals(1, $player->getSkill());
        $this->assertEquals(1, $player->getIntellect());
    }

    public function testRegisterClassFour()
    {
        $this->registerClass(4);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->assertEquals(500, $player->getHealth());
        $this->assertEquals(500, $player->getMaxHealth());
        $this->assertEquals(9, $player->getKi());
        $this->assertEquals(9, $player->getMaxKi());
        $this->assertEquals(4, $player->getStrength());
        $this->assertEquals(1, $player->getResistance());
        $this->assertEquals(1, $player->getAccuracy());
        $this->assertEquals(1, $player->getAgility());
        $this->assertEquals(1, $player->getVision());
        $this->assertEquals(1, $player->getAnalysis());
        $this->assertEquals(17, $player->getSkill());
        $this->assertEquals(7, $player->getIntellect());
    }

    public function testRegisterClassFive()
    {
        $this->registerClass(5);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->assertEquals(500, $player->getHealth());
        $this->assertEquals(500, $player->getMaxHealth());
        $this->assertEquals(1, $player->getKi());
        $this->assertEquals(1, $player->getMaxKi());
        $this->assertEquals(6, $player->getStrength());
        $this->assertEquals(2, $player->getResistance());
        $this->assertEquals(4, $player->getAccuracy());
        $this->assertEquals(4, $player->getAgility());
        $this->assertEquals(15, $player->getVision());
        $this->assertEquals(1, $player->getAnalysis());
        $this->assertEquals(1, $player->getSkill());
        $this->assertEquals(4, $player->getIntellect());
    }

    public function testRegisterClassSix()
    {
        $this->registerClass(6);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->assertEquals(500, $player->getHealth());
        $this->assertEquals(500, $player->getMaxHealth());
        $this->assertEquals(1, $player->getKi());
        $this->assertEquals(1, $player->getMaxKi());
        $this->assertEquals(5, $player->getStrength());
        $this->assertEquals(3, $player->getResistance());
        $this->assertEquals(4, $player->getAccuracy());
        $this->assertEquals(3, $player->getAgility());
        $this->assertEquals(5, $player->getVision());
        $this->assertEquals(15, $player->getAnalysis());
        $this->assertEquals(1, $player->getSkill());
        $this->assertEquals(1, $player->getIntellect());
    }

    public function registerClass($class)
    {
        $crawler = $this->client->request('GET', '/register');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('#player_registration_name'));
        $this->assertCount(1, $crawler->filter('#player_registration_username'));
        $this->assertCount(1, $crawler->filter('#player_registration_password_password'));
        $this->assertCount(1, $crawler->filter('#player_registration_password_password_confirm'));
        $this->assertCount(1, $crawler->filter('#player_registration_email_email'));
        $this->assertCount(1, $crawler->filter('#player_registration_email_email_confirm'));
        $this->assertCount(1, $crawler->filter('#player_registration_class'));

        $form = $crawler->filter('#register-form')->form();
        $form->disableValidation();
        // set some values
        $form['player_registration[name]'] = 'Test';
        $form['player_registration[username]'] = 'My Name is Sparta';
        $form['player_registration[password][password]'] = 'test';
        $form['player_registration[password][password_confirm]'] = 'test';
        $form['player_registration[email][email]'] = 'test@test.com';
        $form['player_registration[email][email_confirm]'] = 'test@test.com';
        $form['player_registration[class]'] = $class;
        $form['player_registration[race]'] = '1';
        $form['player_registration[appearance][type]'] = 'H3';
        $form['player_registration[appearance][image]'] = 'H1.png';

        // submit the form
        $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'account.created'
                ]
            ],
            $session->getBag('flashes')->all()
        );
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/'),
            'response is a redirect to /'
        );
    }
}
