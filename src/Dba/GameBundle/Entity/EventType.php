<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventType
 *
 * @ORM\Table(name="event_type", indexes={@ORM\Index(name="event_type_name", columns={"name"})})
 * @ORM\Entity
 */
class EventType
{
    const PLAYER = 1;
    const HQ = 2;
    const BANK = 3;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

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
     * Set name
     *
     * @param string $name
     *
     * @return EventType
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
}
