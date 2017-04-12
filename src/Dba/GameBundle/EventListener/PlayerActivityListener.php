<?php

namespace Dba\GameBundle\EventListener;

use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Dba\GameBundle\Entity\Player;

class PlayerActivityListener
{
    protected $context;
    protected $em;

    /**
     * Constructor
     *
     * @param TokenStorage $context Token context
     * @param EntityManager $manager Entity manager
     *
     */
    public function __construct(TokenStorage $context, EntityManager $manager)
    {
        $this->context = $context;
        $this->em = $manager;
    }

    /**
     * Update the player "last_login" on each request
     *
     * @param FilterControllerEvent $event Event
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        // Only execute on master request or no context token
        if ($event->getRequestType() !== HttpKernel::MASTER_REQUEST or !$this->context->getToken()) {
            return;
        }

        $player = $this->context->getToken()->getUser();
        if ($player instanceof Player && !$player->isConnected() && $player->isEnabled()) {
            $player->setLastLogin(new DateTime());
            $this->em->flush($player);
        }
    }
}
