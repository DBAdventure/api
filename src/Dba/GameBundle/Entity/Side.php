<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Side
 *
 * @ORM\Table(name="side", uniqueConstraints={@ORM\UniqueConstraint(name="side_name", columns={"name"})})
 * @ORM\Entity
 */
class Side
{
    const GOOD = 1;
    const BAD = 2;
    const NPC = 3;

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
     * @return Side
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
