<?php

namespace Dba\GameBundle\Command;

use Dba\GameBundle\Command\ServicesService;
use Dba\GameBundle\Command\RepositoryService;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class BaseCommand extends ContainerAwareCommand
{
    const TAB = "\t";

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->getContainer()->get('logger');
    }

    /**
     * @return ServicesService
     */
    public function services()
    {
        return $this->getContainer()->get('dba.game.services');
    }

    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * @return RepositoryService
     */
    public function repos()
    {
        return $this->getContainer()->get('dba.game.repos');
    }

    /**
     * Get config parameter
     *
     * @param string $parameter
     */
    public function getParameter($parameter)
    {
        return $this->getContainer()->getParameter($parameter);
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
        return $this->getContainer()->get('translator')->trans($message, $params, $domain);
    }
}
