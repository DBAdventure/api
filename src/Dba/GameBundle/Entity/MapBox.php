<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MapBox
 *
 * @ORM\Table(name="map_box", indexes={@ORM\Index(name="map_image_id", columns={"map_image_id"}),
 * @ORM\Index(name="map_bonus_id", columns={"map_bonus_id"})},
 * uniqueConstraints={@ORM\UniqueConstraint(name="map_box_unique", columns={"map_id", "x", "y"})})
 * @ORM\Entity
 */
class MapBox
{
    const MINIMAP_SIZE = 5;

    /**
     * @var int
     *
     * @ORM\Column(name="damage", type="integer", nullable=false)
     */
    private $damage = '0';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Map", fetch="EAGER", cascade={"persist"}))
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="map_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $map;

    /**
     * @var int
     *
     * @ORM\Column(name="x", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $x;

    /**
     * @var int
     *
     * @ORM\Column(name="y", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $y;

    /**
     * @var MapImage
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\MapImage", fetch="EAGER")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="map_image_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $mapImage;

    /**
     * @var MapBonus
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\MapBonus", fetch="EAGER")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="map_bonus_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $mapBonus;

    /**
     * Set damage
     *
     * @param int $damage
     *
     * @return MapBox
     */
    public function setDamage($damage)
    {
        $this->damage = $damage;

        return $this;
    }

    /**
     * Get damage
     *
     * @return int
     */
    public function getDamage()
    {
        return $this->damage;
    }

    /**
     * Set map
     *
     * @param Map $map
     *
     * @return MapBox
     */
    public function setMap(Map $map)
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
     * Set x
     *
     * @param int $x
     *
     * @return MapBox
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
     * @return MapBox
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
     * Set mapImage
     *
     * @param MapImage $mapImage
     *
     * @return MapBox
     */
    public function setMapImage(MapImage $mapImage)
    {
        $this->mapImage = $mapImage;

        return $this;
    }

    /**
     * Get mapImage
     *
     * @return MapImage
     */
    public function getMapImage()
    {
        return $this->mapImage;
    }

    /**
     * Set mapBonus
     *
     * @param MapBonus $mapBonus
     *
     * @return MapBox
     */
    public function setMapBonus(MapBonus $mapBonus)
    {
        $this->mapBonus = $mapBonus;

        return $this;
    }

    /**
     * Get mapBonus
     *
     * @return MapBonus
     */
    public function getMapBonus()
    {
        return $this->mapBonus;
    }
}
