<?php

namespace Dba\GameBundle\Tests\Controller;

class InboxControllerTest extends BaseTestCase
{
    protected $xhrHeaders = [
        'HTTP_X-Requested-With' => 'XMLHttpRequest',
    ];

    protected $guild;

    public function setUp()
    {
        parent::setUp();
        $player = $this->login();
        $this->guild = $this->createGuild($player, 'DBA');
    }

    public function testInbox()
    {
        $this->client->request('GET', '/inbox');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testInboxInJson()
    {
        $this->client->request('GET', '/inbox', [], [], $this->xhrHeaders);
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testOutbox()
    {
        $this->client->request('GET', '/inbox/outbox');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testOutboxInJson()
    {
        $this->client->request('GET', '/inbox/outbox', [], [], $this->xhrHeaders);
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testArchive()
    {
        $this->client->request('GET', '/inbox/archive');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testArchiveInJson()
    {
        $this->client->request('GET', '/inbox/archive', [], [], $this->xhrHeaders);
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testWriteInJson()
    {
        $this->client->request('GET', '/inbox/write', [], [], $this->xhrHeaders);
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testWrite()
    {
        $this->createPlayer('bast');
        $crawler = $this->client->request('GET', '/inbox/write');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->filter('#inbox-write-form')->form();
        $form->disableValidation();
        // set some values
        $form['inbox_message[subject]'] = 'Test subject';
        $form['inbox_message[message]'] = 'New message';
        $form['inbox_message[recipients][0][name]'] = 'bast';

        // submit the form
        $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'Message sent to bast'
                ]
            ],
            $session->getBag('flashes')->all()
        );
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inbox'),
            'response is a redirect to /inbox'
        );
    }

    public function testWriteToGuild()
    {
        $player = $this->createPlayer('bast');
        $this->joinGuild($this->guild, $player);
        $this->em()->refresh($this->guild);
        $crawler = $this->client->request('GET', '/inbox/write/guild');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->filter('#inbox-write-form')->form();
        $form->disableValidation();
        // set some values
        $form['inbox_message[subject]'] = 'Test subject';
        $form['inbox_message[message]'] = 'New message';

        // submit the form
        $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            [
                'success' => [
                    'Message sent to bast'
                ]
            ],
            $session->getBag('flashes')->all()
        );
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inbox'),
            'response is a redirect to /inbox'
        );
    }

    public function testWriteToUndefinedUser()
    {
        $crawler = $this->client->request('GET', '/inbox/write');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->filter('#inbox-write-form')->form();
        $form->disableValidation();
        // set some values
        $form['inbox_message[subject]'] = 'Test subject';
        $form['inbox_message[message]'] = 'New message';
        $form['inbox_message[recipients][0][name]'] = 'bast';

        // submit the form
        $this->client->submit($form);
        $session = $this->client->getContainer()->get('session');
        $this->assertEquals(
            [],
            $session->getBag('flashes')->all()
        );
        $this->assertEquals(
            302,
            $this->client->getResponse()->getStatusCode()
        );

        $this->assertTrue(
            $this->client->getResponse()->isRedirect('/inbox'),
            'response is a redirect to /inbox'
        );
    }

    public function testReadWithoutMessage()
    {
        $this->client->request('GET', '/inbox/read/1');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
