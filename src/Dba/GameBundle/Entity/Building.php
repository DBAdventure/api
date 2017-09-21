<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Building
 *
 * @ORM\Table(name="building", uniqueConstraints={@ORM\UniqueConstraint(name="building_name", columns={"name"})},
              indexes={@ORM\Index(name="building_map_id", columns={"map_id"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\BuildingRepository")
 * @JMS\ExclusionPolicy("all")
 */
class Building
{
    const TYPE_TELEPORT = 1;
    const TYPE_WANTED = 3;
    const TYPE_BANK = 12;
    const TYPE_MAGIC = 13;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    private $image;

    /**
     * @var integer
     *
     * @ORM\Column(name="x", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $x;

    /**
     * @var integer
     *
     * @ORM\Column(name="y", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $y;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    private $type;

    /**
     * @var integer
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
     *   @ORM\JoinColumn(name="map_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $map;

    /**
     * @var boolean
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
     * @param integer $x
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
     * @return integer
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set type
     *
     * @param integer $type
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
     * @return integer
     */
    public function getType()
    {
        return $this->type;
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
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Guild
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }
}
