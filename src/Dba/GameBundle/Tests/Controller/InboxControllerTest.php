<?php

namespace Dba\GameBundle\Tests\Controller;

class InboxControllerTest extends BaseTestCase
{
    protected $guild;

    public function setUp()
    {
        parent::setUp();
        $player = $this->login();
        $this->guild = $this->createGuild($player, 'DBA');
    }

    public function testInbox()
    {
        $this->client->request('GET', '/api/inbox/inbox');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testOutbox()
    {
        $this->client->request('GET', '/api/inbox/outbox');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testArchive()
    {
        $this->client->request('GET', '/api/inbox/archive');
        $this->assertJsonResponse($this->client->getResponse());
    }

    public function testWrite()
    {
        $this->createPlayer('bast');
        $this->client->request(
            'POST',
            '/api/inbox/write',
            [
                'inbox_message' => [
                    'subject' => 'Test subject',
                    'message' => 'New message',
                    'recipients' => [
                        ['name' => 'bast'],
                    ],
                ],
            ]
        );
        $this->assertJsonResponse($this->client->getResponse(), 201);
    }

    public function testReadWithoutMessage()
    {
        $this->client->request('GET', '/api/inbox/read/1');
        $this->assertJsonresponse($this->client->getResponse(), 404);
    }
}
