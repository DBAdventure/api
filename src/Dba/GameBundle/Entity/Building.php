<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Building
 *
 * @ORM\Table(name="building", indexes={@ORM\Index(name="building_map_id", columns={"map_id"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\BuildingRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Building
{
    const TYPE_TELEPORT = 1;
    const TYPE_WANTED = 3;
    const TYPE_BANK = 12;
    const TYPE_MAGIC = 13;
    const TYPE_DOOR = 14;

    const TYPE_LIST = [
        self::TYPE_TELEPORT => 'teleport',
        self::TYPE_WANTED => 'wanted',
        self::TYPE_BANK => 'bank',
        self::TYPE_MAGIC => 'magic',
        self::TYPE_DOOR => 'door',

        GameObject::TYPE_UNIQUE => 'shop unique',
        GameObject::TYPE_CONSUMABLE => 'shop consumable',
        GameObject::TYPE_VISION => 'shop vision',
        GameObject::TYPE_WEAPON => 'shop weapon',
        GameObject::TYPE_SHIELD => 'shop shield',
        GameObject::TYPE_ACCESSORY => 'shop accessory',
        GameObject::TYPE_CAP => 'shop cap',
        GameObject::TYPE_SHOES => 'shop shoes',
        GameObject::TYPE_OUTFIT => 'shop outfit',
    ];

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     * @Assert\NotBlank
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=50, nullable=false)
     * @Assert\NotBlank
     * @JMS\Expose
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="x", type="integer", nullable=false)
     * @Assert\NotBlank
     */
    private $x;

    /**
     * @var int
     *
     * @ORM\Column(name="y", type="integer", nullable=false)
     * @Assert\NotBlank
     */
    private $y;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     * @Assert\NotBlank
     * @JMS\Expose
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var Map
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Map", fetch="EAGER")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="map_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $map;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default": false})
     */
    private $enabled = false;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Building
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Building
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set x
     *
     * @param int $x
     *
     * @return Building
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
     * @return Building
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
     * Set type
     *
     * @param int $type
     *
     * @return Building
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
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
     * @return Building
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
     * Get image path
     *
     * @return string
     */
    public function getImagePath()
    {
        return sprintf('/bundles/dbaadmin/images/%s', $this->getImage());
    }

    /**
     * Is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param bool $enabled
     *
     * @return Guild
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }
}
