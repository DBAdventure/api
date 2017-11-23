<?php

namespace Dba\GameBundle\Tests\Controller;

class DefaultControllerTest extends BaseTestCase
{
    public function testConfirm()
    {
        $this->registerClass(1);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->client->request(
            'POST',
            '/api/account/confirm/' . $player->getId() . '/' . $player->getConfirmationToken()
        );
        $this->assertJsonResponse($this->client->getResponse());
    }

    /**
     * @ExpectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testConfirmWithWrongId()
    {
        $this->registerClass(1);
        $player = $this->repos()->getPlayerRepository()->findOneByEmail('test@test.com');
        $this->client->request('POST', '/api/account/confirm/99999/' . $player->getConfirmationToken());
        $this->assertJsonResponse($this->client->getResponse(), 400);
    }


    /**
     * @ExpectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testConfirmWithWrongToken()
    {
        $this->client->request('POST', '/api/account/confirm/99999/badToken');
        $this->assertJsonResponse($this->client->getResponse(), 400);
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
        $this->client->request(
            'POST',
            '/api/register',
            [
                'player_registration' => [
                    'name' => 'Test',
                    'username' => 'My name is GoT',
                    'password' => 'test',
                    'password_confirm' => 'test',
                    'email' => 'test@test.com',
                    'email_confirm' => 'test@test.com',
                    'class' => $class,
                    'race' => '1',
                    'side' => '1',
                    'appearance' => [
                        'type' => 'H3',
                        'image' => 'H1.png',
                    ]
                ]
            ]
        );

        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testRegisterWithInvalidData()
    {
        $this->client->request(
            'POST',
            '/api/register',
            [
                'player_registration' => [
                    'name' => '',
                    'username' => '',
                    'password' => 'test',
                    'password_confirm' => 'test',
                    'email' => 'test@test.com',
                    'email_confirm' => 'test@test.com',
                    'race' => '1',
                    'side' => '1',
                    'appearance' => [
                        'type' => 'H3',
                        'image' => 'H1.png',
                    ]
                ]
            ]
        );

        $this->assertJsonResponse($this->client->getResponse(), 400);
    }

    public function testRegisterWithAlreadyConnected()
    {
        $this->login();
        $this->client->request(
            'POST',
            '/api/register'
        );

        $this->assertJsonResponse($this->client->getResponse(), 403);
    }
}
