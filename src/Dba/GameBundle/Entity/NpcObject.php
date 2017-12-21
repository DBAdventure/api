<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Npc Object
 *
 * @ORM\Table(name="npc_object")
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\NpcObjectRepository")
 */
class NpcObject
{
    /**
     * @var integer
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
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var array
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Dba\GameBundle\Entity\Race", cascade={"persist"})
     */
    private $races;

    /**
     * @var integer
     *
     * @ORM\Column(name="luck", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $luck = 100;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->races = new ArrayCollection();
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
     * Set luck
     *
     * @param string $luck
     *
     * @return QuestObject
     */
    public function setLuck($luck)
    {
        $this->luck = $luck;

        return $this;
    }

    /**
     * Get luck
     *
     * @return integer
     */
    public function getLuck()
    {
        return $this->luck;
    }

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
     * Set npc object
     *
     * @param NpcObject $npcObject
     *
     * @return Quest
     */
    public function setNpcObject($npcObject)
    {
        $this->npcObject = $npcObject;

        return $this;
    }

    /**
     * Get npc object
     *
     * @return NpcObject
     */
    public function getNpcObject()
    {
        return $this->npcObject;
    }

    /**
     * Add Race
     *
     * @param Race $race
     *
     * @return NpcObject
     */
    public function addRace(Race $race)
    {
        if (!$this->races->contains($race)) {
            $this->races[] = $race;
        }

        return $this;
    }

    /**
     * Set Races
     *
     * @param Collection $races
     *
     * @return NpcObject
     */
    public function setRaces(Collection $races = null)
    {
        $this->races = new ArrayCollection();
        if (is_null($races)) {
            return;
        }

        foreach ($races as $race) {
            $this->addRace($race);
        }
    }

    /**
     * Remove Race
     *
     * @param Race $race
     */
    public function removeRace(Race $race)
    {
        $this->races->removeElement($race);
    }

    /**
     * Get Races
     *
     * @return ArrayCollection
     */
    public function getRaces()
    {
        return $this->races;
    }

    /**
     * Remove duplicates in collection
     *
     * @return NpcObject
     */
    public function removeDuplicates()
    {
        $races = $this->getRaces();
        $this->setRaces();
        $this->setRaces($races);
        return $this;
    }
}
