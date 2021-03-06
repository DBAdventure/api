<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Map
 *
 * @ORM\Table(name="map")
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\MapRepository")
 */
class Map
{
    const HELL = 1;
    const HEAVEN = 2;
    const ISLAND = 6;
    const TUTORIAL = 7;

    const TYPE_NORMAL = 0;
    const TYPE_TUTORIAL = 1;
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="max_x", type="integer", nullable=false)
     */
    private $maxX;

    /**
     * @var int
     *
     * @ORM\Column(name="max_y", type="integer", nullable=false)
     */
    private $maxY;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false, options={"default": 0})
     */
    private $type;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Map
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
     * Set maxX
     *
     * @param int $maxX
     *
     * @return Map
     */
    public function setMaxX($maxX)
    {
        $this->maxX = $maxX;

        return $this;
    }

    /**
     * Get maxX
     *
     * @return int
     */
    public function getMaxX()
    {
        return $this->maxX;
    }

    /**
     * Set maxY
     *
     * @param int $maxY
     *
     * @return Map
     */
    public function setMaxY($maxY)
    {
        $this->maxY = $maxY;

        return $this;
    }

    /**
     * Get maxY
     *
     * @return int
     */
    public function getMaxY()
    {
        return $this->maxY;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Map
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
     * Check if it's respawn map
     *
     * @return bool
     */
    public function isRespawn()
    {
        return in_array($this->getId(), [self::HEAVEN, self::HELL]);
    }

    /**
     * Check if it's tutorial map
     *
     * @return bool
     */
    public function isTutorial()
    {
        return $this->getType() == self::TYPE_TUTORIAL;
    }
}
