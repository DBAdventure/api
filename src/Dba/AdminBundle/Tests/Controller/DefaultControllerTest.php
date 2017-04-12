<?php

namespace Dba\AdminBundle\Tests\Controller;

use Dba\GameBundle\Tests\Controller\BaseTestCase;

class DefaultControllerTest extends BaseTestCase
{
    public function testIndexWithoutLogin()
    {
        $this->client->request('GET', '/admin');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testIndex()
    {
        $this->login();
        $this->client->request('GET', '/admin');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
