<?php

namespace Dba\GameBundle\Event;

use Dba\GameBundle\Entity\Player;
use Symfony\Component\EventDispatcher\Event;

class ActionEvent extends Event
{
    /**
     * @var Player
     */
    protected $player;

    /**
     * @var Player
     */
    protected $target;

    /**
     * @var array
     */
    protected $data;

    /**
     * Set player
     *
     * @param Player $player Player
     *
     * @return SpellEvent
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;
        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set target
     *
     * @param Player $target Player
     *
     * @return SpellEvent
     */
    public function setTarget(Player $target)
    {
        $this->target = $target;
        return $this;
    }

    /**
     * Get Target
     *
     * @return Player
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set Data
     *
     * @param array $data Data
     *
     * @return SpellEvent
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get Data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
