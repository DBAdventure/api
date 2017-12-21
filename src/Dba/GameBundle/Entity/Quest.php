<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Quest
 *
 * @ORM\Table(name="quest", indexes={@ORM\Index(name="quest_map_id", columns={"map_id"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\QuestRepository")
 */
class Quest
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="npc_name", type="string", length=80, nullable=false)
     * @Assert\NotBlank()
     */
    private $npcName;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="history", type="text", nullable=false)
     */
    private $history;

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
     * @var Map
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Map", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="map_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $map;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default": false})
     */
    private $enabled = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="lifetime", type="integer", nullable=false)
     */
    private $lifetime = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="gain_battle_points", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $gainBattlePoints = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="gain_zeni", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $gainZeni = 0;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuestGainObject", mappedBy="quest", cascade={"persist"})
     */
    private $gainObjects;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuestObject", mappedBy="quest", cascade={"persist"})
     */
    private $objectsNeeded;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuestNpcObject", mappedBy="quest", cascade={"persist"})
     */
    private $npcObjectsNeeded;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="QuestNpc", mappedBy="quest", cascade={"persist"})
     */
    private $npcsNeeded;

    /**
     * @var array
     *
     * @ORM\Column(name="requirements", type="json_array", nullable=false, options={"default": "{}"})
     */
    private $requirements = [];

    /**
     * @var array
     *
     * @ORM\Column(name="on_accepted", type="json_array", nullable=false, options={"default": "{}"})
     */
    private $onAccepted = [];

    /**
     * @var array
     *
     * @ORM\Column(name="on_completed", type="json_array", nullable=false, options={"default": "{}"})
     */
    private $onCompleted = [];

    /**
     * @var array
     *
     * @ORM\Column(name="on_finished", type="json_array", nullable=false, options={"default": "{}"})
     */
    private $onFinished = [];

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Quest
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
     * Set history
     *
     * @param string $history
     *
     * @return Quest
     */
    public function setHistory($history)
    {
        $this->history = $history;

        return $this;
    }

    /**
     * Get history
     *
     * @return string
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set npc name
     *
     * @param string $npcName
     *
     * @return Quest
     */
    public function setNpcName($npcName)
    {
        $this->npcName = $npcName;

        return $this;
    }

    /**
     * Get npc name
     *
     * @return string
     */
    public function getNpcName()
    {
        return $this->npcName;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Quest
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
     * @return Quest
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
     * @return Quest
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
     * @return Quest
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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gainObjects = new ArrayCollection();
        $this->objectsNeeded = new ArrayCollection();
        $this->npcObjectsNeeded = new ArrayCollection();
        $this->npcsNeeded = new ArrayCollection();
    }

    /**
     * Set lifetime
     *
     * @param integer $lifetime
     *
     * @return Quest
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * Get lifetime
     *
     * @return integer
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Set gainBattlePoints
     *
     * @param integer $gainBattlePoints
     *
     * @return Quest
     */
    public function setGainBattlePoints($gainBattlePoints)
    {
        $this->gainBattlePoints = $gainBattlePoints;

        return $this;
    }

    /**
     * Get gainBattlePoints
     *
     * @return integer
     */
    public function getGainBattlePoints()
    {
        return $this->gainBattlePoints;
    }

    /**
     * Set gainZeni
     *
     * @param integer $gainZeni
     *
     * @return Quest
     */
    public function setGainZeni($gainZeni)
    {
        $this->gainZeni = $gainZeni;

        return $this;
    }

    /**
     * Get gainZeni
     *
     * @return integer
     */
    public function getGainZeni()
    {
        return $this->gainZeni;
    }

    /**
     * Set requirements
     *
     * @param array $requirements
     *
     * @return Quest
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
     * Set on Accepted
     *
     * @param array $onAccepted
     *
     * @return Quest
     */
    public function setOnAccepted($onAccepted)
    {
        $this->onAccepted = $onAccepted;

        return $this;
    }

    /**
     * Get on Accepted
     *
     * @return array
     */
    public function getOnAccepted()
    {
        return $this->onAccepted;
    }

    /**
     * Set onFinished
     *
     * @param array $onFinished
     *
     * @return Quest
     */
    public function setOnFinished($onFinished)
    {
        $this->onFinished = $onFinished;

        return $this;
    }

    /**
     * Get onFinished
     *
     * @return array
     */
    public function getOnFinished()
    {
        return $this->onFinished;
    }

    /**
     * Set onCompleted
     *
     * @param array $onCompleted
     *
     * @return Quest
     */
    public function setOnCompleted($onCompleted)
    {
        $this->onCompleted = $onCompleted;

        return $this;
    }

    /**
     * Get onCompleted
     *
     * @return array
     */
    public function getOnCompleted()
    {
        return $this->onCompleted;
    }

    /**
     * Add gainObject
     *
     * @param Object $gainObject
     *
     * @return Quest
     */
    public function addGainObject(Object $gainObject)
    {
        $this->gainObjects[] = $gainObject;

        return $this;
    }

    /**
     * Remove gainObject
     *
     * @param Object $gainObject
     */
    public function removeGainObject(Object $gainObject)
    {
        $this->gainObjects->removeElement($gainObject);
    }

    /**
     * Get gainObjects
     *
     * @return ArrayCollection
     */
    public function getGainObjects()
    {
        return $this->gainObjects;
    }

    /**
     * Add objectsNeeded
     *
     * @param QuestObject $objectsNeeded
     *
     * @return Quest
     */
    public function addObjectsNeeded(QuestObject $objectsNeeded)
    {
        $this->objectsNeeded[] = $objectsNeeded;

        return $this;
    }

    /**
     * Remove objectsNeeded
     *
     * @param QuestObject $objectsNeeded
     */
    public function removeObjectsNeeded(QuestObject $objectsNeeded)
    {
        $this->objectsNeeded->removeElement($objectsNeeded);
    }

    /**
     * Get objectsNeeded
     *
     * @return ArrayCollection
     */
    public function getObjectsNeeded()
    {
        return $this->objectsNeeded;
    }

    /**
     * Add npcObjectsNeeded
     *
     * @param QuestNpcObject $npcObjectsNeeded
     *
     * @return Quest
     */
    public function addNpcObjectsNeeded(QuestNpcObject $npcObjectsNeeded)
    {
        $this->npcObjectsNeeded[] = $npcObjectsNeeded;

        return $this;
    }

    /**
     * Remove npcObjectsNeeded
     *
     * @param QuestNpcObject $npcObjectsNeeded
     */
    public function removeNpcObjectsNeeded(QuestNpcObject $npcObjectsNeeded)
    {
        $this->npcObjectsNeeded->removeElement($npcObjectsNeeded);
    }

    /**
     * Get npcObjectsNeeded
     *
     * @return ArrayCollection
     */
    public function getNpcObjectsNeeded()
    {
        return $this->npcObjectsNeeded;
    }

    /**
     * Add npcsNeeded
     *
     * @param QuestNpc $npcsNeeded
     *
     * @return Quest
     */
    public function addNpcsNeeded(QuestNpc $npcsNeeded)
    {
        $this->npcsNeeded[] = $npcsNeeded;

        return $this;
    }

    /**
     * Remove npcsNeeded
     *
     * @param QuestNpc $npcsNeeded
     */
    public function removeNpcsNeeded(QuestNpc $npcsNeeded)
    {
        $this->npcsNeeded->removeElement($npcsNeeded);
    }

    /**
     * Get npcsNeeded
     *
     * @return ArrayCollection
     */
    public function getNpcsNeeded()
    {
        return $this->npcsNeeded;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;

            $fields = ['npcsNeeded', 'gainObjects', 'objectsNeeded', 'npcObjectsNeeded'];
            foreach ($fields as $field) {
                $items = new ArrayCollection();
                foreach ($this->{$field} as $item) {
                    $clone = clone $item;
                    $clone->setQuest($this);
                    $items->add($clone);
                }
                $this->{$field} = $items;
            }
        }
    }
}
