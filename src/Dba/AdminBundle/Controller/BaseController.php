<?php

namespace Dba\AdminBundle\Controller;

use Dba\GameBundle\Services;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller
{
    /**
     * @return Services\RepositoryService
     */
    public function repos()
    {
        return $this->get('dba.game.repos');
    }

    /**
     * @return Services\Service
     */
    public function services()
    {
        return $this->get('dba.game.services');
    }

    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * Translate message
     *
     * @param string $message Message
     * @param array $params Message parameters
     * @param string $domain Domain
     *
     * @return string
     */
    public function trans($message, array $params = [], $domain = null)
    {
        return $this->get('translator')->trans($message, $params, $domain);
    }
}
