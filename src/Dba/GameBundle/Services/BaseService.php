<?php

namespace Dba\GameBundle\Services;

use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class BaseService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->container->get('logger');
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->container->get('session');
    }

    /**
     * @return ServicesService
     */
    public function services()
    {
        return $this->container->get('dba.game.services');
    }

    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * @return RepositoryService
     */
    public function repos()
    {
        return $this->container->get('dba.game.repos');
    }

    /**
     * @return Player
     */
    public function getUser()
    {
        $tokenStorage = $this->container->get('security.token_storage');
        if (empty($tokenStorage->getToken())) {
            return;
        }

        return $tokenStorage->getToken()->getUser();
    }

    /**
     * Get config parameter
     *
     * @param string $parameter
     */
    public function getParameter($parameter)
    {
        return $this->container->getParameter($parameter);
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
        return $this->container->get('translator')->trans($message, $params, $domain);
    }
}
