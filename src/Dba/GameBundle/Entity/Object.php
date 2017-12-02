<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Object
 *
 * @ORM\Table(name="object", uniqueConstraints={@ORM\UniqueConstraint(name="object_name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\ObjectRepository")
 */
class Object
{
    const DEFAULT_MAP = 1;
    const DEFAULT_SENZU = 2;
    const DEFAULT_POTION_OF_LIFE = 3;
    const DEFAULT_POTION_OF_FATIGUE = 13;
    const DEFAULT_PEAR = 20;
    const DEFAULT_BERRIES = 27;

    const BONUS_HEALTH_PERCENT = 'health_percent';
    const BONUS_HEALTH = 'health';
    const BONUS_FATIGUE_PERCENT = 'fatigue_percent';
    const BONUS_FATIGUE = 'fatigue';
    const BONUS_KI_PERCENT = 'ki_percent';
    const BONUS_KI = 'ki';
    const BONUS_ACTION_POINT = 'action_point';
    const BONUS_MOVEMENT_POINT = 'movement_point';
    const BONUS_TELEPORT = 'teleport';

    const BONUS_MAXIMUM_KI = 'max_ki';
    const BONUS_MAXIMUM_HEALTH = 'max_health';
    const BONUS_STRENGTH = 'strength';
    const BONUS_ACCURACY = 'accuracy';
    const BONUS_AGILITY = 'agility';
    const BONUS_ANALYSIS = 'analysis';
    const BONUS_SKILL = 'skill';
    const BONUS_INTELLECT = 'intellect';
    const BONUS_RESISTANCE = 'resistance';
    const BONUS_VISION = 'vision';

    const LEVEL = 'level';

    const TYPE_SPECIAL = 0;
    const TYPE_UNIQUE = 2;
    const TYPE_CONSUMABLE = 4;
    const TYPE_VISION = 5;
    const TYPE_WEAPON = 6;
    const TYPE_SHIELD = 7;
    const TYPE_ACCESSORY = 8;
    const TYPE_CAP = 9;
    const TYPE_SHOES = 10;
    const TYPE_OUTFIT = 11;

    const BONUS_LIST = [
        self::BONUS_HEALTH_PERCENT => 'bonus.percent.health',
        self::BONUS_HEALTH => 'bonus.health',
        self::BONUS_FATIGUE_PERCENT => 'bonus.percent.fatigue',
        self::BONUS_FATIGUE => 'bonus.fatigue',
        self::BONUS_KI_PERCENT => 'bonus.percent.ki',
        self::BONUS_KI => 'bonus.ki',
        self::BONUS_TELEPORT => 'bonus.teleport',
        self::BONUS_ACTION_POINT => 'bonus.points.action',
        self::BONUS_MOVEMENT_POINT => 'bonus.points.movement',

        self::BONUS_MAXIMUM_KI => 'bonus.max.ki',
        self::BONUS_MAXIMUM_HEALTH => 'bonus.max.health',

        self::BONUS_STRENGTH => 'bonus.strength',
        self::BONUS_ACCURACY => 'bonus.accuracy',
        self::BONUS_AGILITY => 'bonus.agility',
        self::BONUS_ANALYSIS => 'bonus.analysis',
        self::BONUS_SKILL => 'bonus.skill',
        self::BONUS_INTELLECT => 'bonus.intellect',
        self::BONUS_RESISTANCE => 'bonus.resistance',
        self::BONUS_VISION => 'bonus.vision',
    ];

    const REQUIREMENTS_LIST = [
        self::BONUS_STRENGTH => 'bonus.strength',
        self::BONUS_ACCURACY => 'bonus.accuracy',
        self::BONUS_AGILITY => 'bonus.agility',
        self::BONUS_ANALYSIS => 'bonus.analysis',
        self::BONUS_SKILL => 'bonus.skill',
        self::BONUS_INTELLECT => 'bonus.intellect',
        self::BONUS_RESISTANCE => 'bonus.resistance',
        self::BONUS_VISION => 'bonus.vision',
        self::LEVEL => 'level',
    ];

    const TYPE_LIST = [
        self::TYPE_SPECIAL => 'special',
        self::TYPE_UNIQUE => 'unique',
        self::TYPE_CONSUMABLE => 'consumable',
        self::TYPE_VISION => 'vision',
        self::TYPE_WEAPON => 'weapon',
        self::TYPE_SHIELD => 'shield',
        self::TYPE_ACCESSORY => 'accessory',
        self::TYPE_CAP => 'cap',
        self::TYPE_SHOES => 'shoes',
        self::TYPE_OUTFIT => 'outfit',
    ];


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
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="price", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=30, nullable=false)
     * @Assert\NotBlank()
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="weight", type="decimal", precision=10, scale=0, nullable=false)
     * @Assert\NotBlank()
     */
    private $weight;

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
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PlayerObject", mappedBy="object", cascade={"persist"})
     */
    private $playerObjects;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default": false})
     */
    private $enabled = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->playerObjects = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Object
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
     * Set description
     *
     * @param string $description
     *
     * @return Object
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set price
     *
     * @param integer $price
     *
     * @return Object
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Object
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
     * Set weight
     *
     * @param string $weight
     *
     * @return Object
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return string
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set bonus
     *
     * @param array $bonus
     *
     * @return Object
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
     * Set type
     *
     * @param integer $type
     *
     * @return Object
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
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
     * Add player
     *
     * @param PlayerObject $playerObject
     *
     * @return Object
     */
    public function addPlayerObject(PlayerObject $playerObject)
    {
        $this->playerObjects[] = $playerObject;
        return $this;
    }

    /**
     * Remove player object
     *
     * @param PlayerObject $playerObject
     */
    public function removePlayerObject(PlayerObject $playerObject)
    {
        $this->player->removeElement($playerObject);
    }

    /**
     * Get player objects
     *
     * @return ArrayCollection
     */
    public function getPlayerObjects()
    {
        return $this->playerObjects;
    }

    /**
     * Get image path
     *
     * @return string
     */
    public function getImagePath()
    {
        return sprintf('/bundles/dbaadmin/images/objects/%s', $this->getImage());
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
}
