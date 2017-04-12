<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bank
 *
 * @ORM\Table(name="bank")
 * @ORM\Entity
 */
class Bank
{
    /**
     * @var integer
     *
     * @ORM\Column(name="zeni", type="integer", nullable=false)
     */
    private $zeni;

    /**
     * @var Player
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Dba\GameBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $player;

    /**
     * Set zeni
     *
     * @param integer $zeni
     *
     * @return Bank
     */
    public function setZeni($zeni)
    {
        $this->zeni = $zeni;

        return $this;
    }

    /**
     * Get zeni
     *
     * @return integer
     */
    public function getZeni()
    {
        return $this->zeni;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return Bank
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
}
