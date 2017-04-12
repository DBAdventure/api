<?php

namespace Dba\GameBundle\Services;

use Swift_Message;
use Dba\GameBundle\Entity\Player;

class MailService extends BaseService
{
    const EMAIL_NO_REPLY = 'no-reply@rpg-dbadventure.com';

    /*
     * Send and email to a player
     *
     * @param Player $player Player who buy
     * @param string $subject Subject of the mail
     * @param string $template Template to use
     * @param array $parameters Template parameters
     *
     * @return boolean
     */
    public function send(Player $player, $subject, $template, array $parameters = [])
    {
        $message = Swift_Message::newInstance()
                 ->setSubject($subject)
                 ->setFrom(self::EMAIL_NO_REPLY)
                 ->setTo($player->getEmail())
                 ->setBody(
                     $this->container->get('templating')->render(
                         'DbaGameBundle::emails/' . $template . '.html.twig',
                         array_merge($parameters, ['player' => $player])
                     ),
                     'text/html'
                 );

        return $this->container->get('mailer')->send($message);
    }
}
