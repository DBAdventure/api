<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rank
 *
 * @ORM\Table(name="rank", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="rank_name", columns={"name"}),
 *     @ORM\UniqueConstraint(name="rank_race_id_level", columns={"race_id", "level"})
 * },
 * indexes={@ORM\Index(name="rank_race_id", columns={"race_id"})})
 * @ORM\Entity
 */
class Rank
{
    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer", nullable=false)
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Race
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Race", fetch="EAGER")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="race_id", referencedColumnName="id", nullable=false)
     * })
     */
    private $race;

    /**
     * Set level
     *
     * @param int $level
     *
     * @return Rank
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Rank
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set race
     *
     * @param Race $race
     *
     * @return Rank
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
}
