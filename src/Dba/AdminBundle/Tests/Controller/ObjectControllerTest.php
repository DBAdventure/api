<?php

namespace Dba\AdminBundle\Tests\Controller;

use Dba\GameBundle\Tests\Controller\BaseTestCase;

class ObjectControllerTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $object = $this->repos()->getObjectRepository()->findOneByName('Test');
        if (!empty($object)) {
            $this->em()->remove($object);
        }
        $object = $this->repos()->getObjectRepository()->findOneByName('Test2');
        if (!empty($object)) {
            $this->em()->remove($object);
        }

        $this->login();
    }

    public function testIndex()
    {
        $this->client->request('GET', '/admin/object');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateWithInvaliData()
    {
        $crawler = $this->client->request('GET', '/admin/object/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#object-form')->form();
        // set some values
        $form['object[name]'] = 'Subject test';
        $form['object[image]'] = 'weapon/roshi.png';

        // submit the form
        $this->client->submit($form);
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', '/admin/object/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#object-form')->form();
        // set some values
        $form['object[name]'] = 'Test';
        $form['object[description]'] = 'Test';
        $form['object[weight]'] = 1;
        $form['object[price]'] = 200;
        $form['object[type]'] = 8;
        $form['object[image]'] = 'weapon/roshi.png';

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
                    'Object created!',
                ],
            ],
            $session->getBag('flashes')->all()
        );
    }

    public function testEdit()
    {
        $this->testCreate();
        $object = $this->repos()->getObjectRepository()->findOneByName('Test');
        $this->assertEquals('Test', $object->getName());
        $crawler = $this->client->request('GET', '/admin/object/edit/' . $object->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#object-form')->form();
        // set some values
        $form['object[name]'] = 'Test2';
        $form['object[image]'] = 'weapon/roshi.png';

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
                    'Object saved!',
                ],
            ],
            $session->getBag('flashes')->all()
        );

        $this->em()->refresh($object);
        $this->assertEquals('Test2', $object->getName());
    }

    public function testDelete()
    {
        $this->testCreate();
        $object = $this->repos()->getObjectRepository()->findOneByName('Test');
        $this->client->request('GET', '/admin/object/delete/' . $object->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->em()->refresh($object);

        $object = $this->repos()->getObjectRepository()->findOneByName('Test');
        $this->assertNull($object);
    }
}
