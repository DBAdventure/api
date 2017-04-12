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

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var integer
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
