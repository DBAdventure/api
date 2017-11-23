<?php

namespace Dba\GameBundle\Tests\Controller;

class MagicControllerTest extends BaseTestCase
{
    public function testTrainingRoomSkill()
    {
        $this->login();
        $this->client->request('GET', '/api/magic/spells');
        $this->assertJsonResponse($this->client->getResponse());
    }
}
