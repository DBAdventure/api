<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Race
 *
 * @ORM\Table(name="race", uniqueConstraints={@ORM\UniqueConstraint(name="race_name", columns={"name"})})
 * @ORM\Entity
 */
class Race
{
    const HUMAN = 1;
    const HUMAN_SAIYAJIN = 2;
    const NAMEKIAN = 3;
    const SAIYAJIN = 4;
    const ALIEN = 5;
    const CYBORG = 6;
    const MAJIN = 7;
    const DRAGON = 8;

    const PREDATOR = 9;
    const GHOST = 10;
    const INSECT = 11;
    const REPTILIAN = 12;
    const DEMON = 13;
    const HYDRA = 14;
    const HUMAN_SOLDIER = 15;

    const NPC_LIST = [
        self::PREDATOR => 'predator',
        self::GHOST => 'ghost',
        self::INSECT => 'insect',
        self::REPTILIAN => 'reptilian',
        self::DEMON => 'demon',
        self::HYDRA => 'hydra',
        self::HUMAN_SOLDIER => 'human-soldier',
    ];

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
     * Set name
     *
     * @param string $name
     *
     * @return Race
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
}
