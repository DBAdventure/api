<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MapBonus
 *
 * @ORM\Table(name="map_bonus", uniqueConstraints={@ORM\UniqueConstraint(name="map_bonus_name", columns={"name"})})
 * @ORM\Entity
 */
class MapBonus
{
    const TYPE_DEFAULT = 0;
    const TYPE_IMPASSABLE = 1;
    const TYPE_RESPAWN = 2;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="bonus", type="json_array", nullable=false)
     */
    private $bonus;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MapBonus
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
     * Set bonus
     *
     * @param array $bonus
     *
     * @return MapBonus
     */
    public function setBonus($bonus)
    {
        $this->bonus = $bonus;

        return $this;
    }

    /**
     * Get bonus
     *
     * @return array
     */
    public function getBonus()
    {
        return $this->bonus;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return MapBonus
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
}
