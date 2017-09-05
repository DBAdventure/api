<?php

namespace Dba\AdminBundle\Tests\Controller;

use Dba\GameBundle\Tests\Controller\BaseTestCase;

class NewsControllerTest extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->login();
    }

    public function testIndex()
    {
        $this->client->request('GET', '/admin/news');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateWithInvaliData()
    {
        $crawler = $this->client->request('GET', '/admin/news/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#news-form')->form();
        // set some values
        $form['news[subject]'] = 'Subject test';
        $form['news[image]'] = '/bundles/dbaadmin/images/avatars/npc_quest/6.png';

        // submit the form
        $this->client->submit($form);
        $this->assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', '/admin/news/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#news-form')->form();
        // set some values
        $form['news[subject]'] = 'Test';
        $form['news[message]'] = 'Message test';
        $form['news[image]'] = '/bundles/dbaadmin/images/avatars/npc_quest/6.png';

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
                    'News created!'
                ]
            ],
            $session->getBag('flashes')->all()
        );

        $news = $this->repos()->getNewsRepository()->findOneBySubject('Test');
        $this->assertEquals($this->login()->getId(), $news->getCreatedBy()->getId());
    }

    public function testEdit()
    {
        $this->testCreate();
        $news = $this->repos()->getNewsRepository()->findOneBySubject('Test');
        $this->assertEquals('Message test', $news->getMessage());
        $crawler = $this->client->request('GET', '/admin/news/edit/' . $news->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->filter('#news-form')->form();
        // set some values
        $form['news[subject]'] = 'Test';
        $form['news[message]'] = 'New message';
        $form['news[image]'] = '/bundles/dbaadmin/images/avatars/npc_quest/6.png';

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
                    'News saved!'
                ]
            ],
            $session->getBag('flashes')->all()
        );

        $this->em()->refresh($news);
        $this->assertEquals('New message', $news->getMessage());
    }

    public function testDelete()
    {
        $this->testCreate();
        $news = $this->repos()->getNewsRepository()->findOneBySubject('Test');
        $this->client->request('GET', '/admin/news/delete/' . $news->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->em()->refresh($news);

        $news = $this->repos()->getNewsRepository()->findOneBySubject('Test');
        $this->assertNull($news);
    }
}
