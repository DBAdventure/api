<?php

namespace Dba\AdminBundle\Tests\Controller;

use Dba\GameBundle\Tests\Controller\BaseTestCase;

class BuildingControllerTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $building = $this->repos()->getBuildingRepository()->findOneByName('Test');
        if (!empty($building)) {
            $this->em()->remove($building);
        }
        $building = $this->repos()->getBuildingRepository()->findOneByName('Test2');
        if (!empty($building)) {
            $this->em()->remove($building);
        }

        $this->login();
    }

    public function testIndex()
    {
        $this->client->request('GET', '/admin/building');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateWithInvaliData()
    {
        $crawler = $this->client->request('GET', '/admin/building/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#building-form')->form();
        // set some values
        $form['building[name]'] = 'Subject test';
        $form['building[image]'] = 'head.png';

        // submit the form
        $this->client->submit($form);
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', '/admin/building/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#building-form')->form();
        // set some values
        $form['building[name]'] = 'Test';
        $form['building[map]'] = 1;
        $form['building[x]'] = 1;
        $form['building[y]'] = 1;
        $form['building[type]'] = 8;
        $form['building[image]'] = 'head.png';

        // submit the form
        $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(
            [
                'success' => [
                    'Building created!'
                ]
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testEdit()
    {
        $this->testCreate();
        $building = $this->repos()->getBuildingRepository()->findOneByName('Test');
        $this->assertEquals('Test', $building->getName());
        $crawler = $this->client->request('GET', '/admin/building/edit/' . $building->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#building-form')->form();
        // set some values
        $form['building[name]'] = 'Test2';
        $form['building[image]'] = 'head.png';

        // submit the form
        $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );
        $this->assertEquals(
            [
                'success' => [
                    'Building saved!'
                ]
            ],
            $session->getBag('flashes')->all()
        );

        $this->em()->refresh($building);
        $this->assertEquals('Test2', $building->getName());
    }

    public function testDelete()
    {
        $this->testCreate();
        $building = $this->repos()->getBuildingRepository()->findOneByName('Test');
        $this->client->request('GET', '/admin/building/delete/' . $building->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->em()->refresh($building);

        $building = $this->repos()->getBuildingRepository()->findOneByName('Test');
        $this->assertNull($building);
    }
}
