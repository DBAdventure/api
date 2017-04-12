<?php
namespace Dba\GameBundle\Services;

use DateTime;
use Symfony\Component\HttpFoundation\Request;

class TemplateService extends BaseService
{
    protected $period;
    protected $authenticationError;

    const PERIOD_DAY = 'day';
    const PERIOD_NIGHT = 'night';

    /**
     * Get period
     *
     * @return string
     */
    public function getPeriod()
    {
        if (empty($this->period)) {
            $date = new DateTime();
            $morning = new DateTime('07:30');
            $evening = new DateTime('19:30');
            $this->period = ($date >= $morning and $date <= $evening) ? self::PERIOD_DAY : self::PERIOD_NIGHT;
        }

        return $this->period;
    }

    /**
     * Get number of good guys
     *
     * @return integer
     */
    public function getNbGoodGuys()
    {
        return $this->repos()->getPlayerRepository()->countBySide(Side::GOOD);
    }

    /**
     * Get number of bad guys
     *
     * @return integer
     */
    public function getNbBadGuys()
    {
        return $this->repos()->getPlayerRepository()->countBySide(Side::BAD);
    }

    /**
     * Get number of Npc
     *
     * @return integer
     */
    public function getNbNpc()
    {
        return $this->repos()->getPlayerRepository()->countBySide(Side::NPC);
    }

    /**
     * Get number of Saiyajins
     *
     * @return integer
     */
    public function getNbSaiyajins()
    {
        return $this->repos()->getPlayerRepository()->countByRace(Race::SAIYAJIN);
    }

    /**
     * Get number of Human Saiyajins
     *
     * @return integer
     */
    public function getNbHumanSaiyajins()
    {
        return $this->repos()->getPlayerRepository()->countByRace(Race::HUMAN_SAIYAJIN);
    }

    /**
     * Get number of humans
     *
     * @return integer
     */
    public function getNbHumans()
    {
        return $this->repos()->getPlayerRepository()->countByRace(Race::HUMAN);
    }

    /**
     * Get number of namekians
     *
     * @return integer
     */
    public function getNbNamekians()
    {
        return $this->repos()->getPlayerRepository()->countByRace(Race::NAMEKIAN);
    }

    /**
     * Get number of dragons
     *
     * @return integer
     */
    public function getNbDragons()
    {
        return $this->repos()->getPlayerRepository()->countByRace(Race::DRAGON);
    }

    /**
     * Get number of aliens
     *
     * @return integer
     */
    public function getNbAliens()
    {
        return $this->repos()->getPlayerRepository()->countByRace(Race::ALIEN);
    }

    /**
     * Get number of cyborgs
     *
     * @return integer
     */
    public function getNbCyborgs()
    {
        return $this->repos()->getPlayerRepository()->countByRace(Race::CYBORG);
    }

    /**
     * Get number of majins
     *
     * @return integer
     */
    public function getNbMajins()
    {
        return $this->repos()->getPlayerRepository()->countByRace(Race::MAJIN);
    }

    /**
     * Get last authentication errors
     *
     * @return integer
     */
    public function getLastAuthenticationError()
    {
        if (empty($this->authenticationError)) {
            $authenticationUtils = $this->container->get('security.authentication_utils');
            // get the login error if there is one
            $this->authenticationError = $authenticationUtils->getLastAuthenticationError();
        }

        return $this->authenticationError;
    }

    /**
     * Get online players
     *
     * @return integer
     */
    public function getOnlinePlayers()
    {
        return count($this->repos()->getPlayerRepository()->getOnlinePlayers());
    }

    /**
     * Get unread messages
     *
     * @return integer
     */
    public function getUnreadMessages()
    {
        if (empty($this->getUser())) {
            return 0;
        }

        return $this->repos()->getInboxRepository()->countUnreadMessages($this->getUser());
    }
}
