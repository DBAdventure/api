<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DragonBall
 *
 * @ORM\Table(name="dragon_ball", indexes={@ORM\Index(name="dragon_ball_map_id", columns={"map_id"}),
 * @ORM\Index(name="dragon_ball_player_id", columns={"player_id"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\DragonBallRepository")
 */
class DragonBall
{
    /**
     * @var int
     *
     * @ORM\Column(name="x", type="integer", nullable=true)
     */
    private $x;

    /**
     * @var int
     *
     * @ORM\Column(name="y", type="integer", nullable=true)
     */
    private $y;

    /**
     * @var int
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Player", fetch="EAGER")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $player;

    /**
     * @var Map
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Map", fetch="EAGER")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="map_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $map;

    /**
     * Set x
     *
     * @param int $x
     *
     * @return DragonBall
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get x
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set y
     *
     * @param int $y
     *
     * @return DragonBall
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get y
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set is visible
     *
     * @param bool $visible
     *
     * @return DragonBall
     */
    public function setVisible($visible)
    {
        $this->visible = (bool) $visible;

        return $this;
    }

    /**
     * Get type
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set map
     *
     * @param Map $map
     *
     * @return DragonBall
     */
    public function setMap(Map $map = null)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get map
     *
     * @return Map
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return DragonBall
     */
    public function setPlayer(Player $player = null)
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
     * Get image path
     *
     * @return string
     */
    public function getImagePath()
    {
        return sprintf('/bundles/dbaadmin/images/dragon-ball/%s.png', $this->getId());
    }

    /**
     * Get visible
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }
}
