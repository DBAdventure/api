<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * MapObject
 *
 * @ORM\Table(name="map_object", indexes={@ORM\Index(name="map_object_object_id", columns={"object_id"}),
              @ORM\Index(name="map_object_map_id", columns={"map_id"}),
              @ORM\Index(name="map_object_map_object_type_id", columns={"map_object_type_id"})})
 * @ORM\Entity
 */
class MapObject
{
    const EXTRA_DIALOGUE = 'dialogue';

    const EXTRA_LIST = [
        self::EXTRA_DIALOGUE => 'extra.dialogue',
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="x", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $x;

    /**
     * @var integer
     *
     * @ORM\Column(name="y", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $y;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer", nullable=true)
     * @JMS\Exclude
     */
    private $number;

    /**
     * @var integer
     *
     * @var MapObjectType
     *
     * @ORM\ManyToOne(targetEntity="MapObjectType", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="map_object_type_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $mapObjectType;

    /**
     * @var Map
     *
     * @ORM\ManyToOne(targetEntity="Map", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="map_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     * @JMS\Exclude
     */
    private $map;

    /**
     * @var Object
     *
     * @ORM\ManyToOne(targetEntity="GameObject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="object_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     * @JMS\Exclude
     */
    private $object;

    /**
     * @var array
     *
     * @ORM\Column(name="extra", type="json_array", nullable=true)
     * @JMS\Exclude
     */
    private $extra;

    /**
     * Set x
     *
     * @param integer $x
     *
     * @return MapObject
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get x
     *
     * @return integer
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set y
     *
     * @param integer $y
     *
     * @return MapObject
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get y
     *
     * @return integer
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return MapObject
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get id
     *
     * @return integer
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
     * @return MapObject
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
     * Set map object type
     *
     * @param MapObjectType $mapObjectType
     *
     * @return MapObject
     */
    public function setMapObjectType(MapObjectType $mapObjectType = null)
    {
        $this->mapObjectType = $mapObjectType;

        return $this;
    }

    /**
     * Get map object type
     *
     * @return MapObjectType
     */
    public function getMapObjectType()
    {
        return $this->mapObjectType;
    }

    /**
     * Set object
     *
     * @param Object $object
     *
     * @return MapObject
     */
    public function setObject(Object $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return Object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Get extra
     *
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set extra
     *
     * @param string $extra
     *
     * @return MapObject
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }
}
