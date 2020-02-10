<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bank
 *
 * @ORM\Table(name="spell",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="spell_name_race_id",
 *         columns={"name", "race_id"})
 *     })
 *     @ORM\Entity
 */
class Spell
{
    const TYPE_ATTACK = 1;
    const TYPE_PLAYER = 2;
    const TYPE_TARGET = 3;
    const TYPE_BOTH = 4;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="bonus", type="json_array", nullable=false)
     */
    private $bonus;

    /**
     * @var array
     *
     * @ORM\Column(name="requirements", type="json_array", nullable=false)
     */
    private $requirements;

    /**
     * @var Race
     *
     * @ORM\ManyToOne(targetEntity="Race", fetch="EAGER")
     * @ORM\JoinColumn(name="race_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $race;

    /**
     * @var int
     *
     * @ORM\Column(name="distance", type="integer", nullable=false)
     */
    private $distance;

    /**
     * @var int
     *
     * @ORM\Column(name="damages", type="integer", nullable=false)
     */
    private $damages;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     */
    private $price;

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
     * Set bonus
     *
     * @param array $bonus
     *
     * @return array
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
     * Set requirements
     *
     * @param array $requirements
     *
     * @return array
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

    /**
     * Get requirements
     *
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * Set race
     *
     * @param Race $race
     *
     * @return Player
     */
    public function setRace(Race $race = null)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get race
     *
     * @return Race
     */
    public function getRace()
    {
        return $this->race;
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
     * Set price
     *
     * @param int $price
     p*
     * @return Spell
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set distance
     *
     * @param int $distance
     *
     * @return Spell
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return int
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set damages
     *
     * @param int $damages
     *
     * @return Spell
     */
    public function setDamages($damages)
    {
        $this->damages = $damages;

        return $this;
    }

    /**
     * Get damages
     *
     * @return int
     */
    public function getDamages()
    {
        return $this->damages;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return Spell
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
}
