<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * PlayerObject
 *
 * @ORM\Table(name="player_object")
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\PlayerObjectRepository")
 */
class PlayerObject
{
    /**
     * @var Player
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="playerObjects", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @JMS\Exclude
     */
    private $player;

    /**
     * @var object
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="GameObject", inversedBy="playerObjects")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $object;

    /**
     * @var int
     *
     * @ORM\Column(name="number", type="integer", nullable=false)
     */
    private $number;

    /**
     * @var bool
     *
     * @ORM\Column(name="equipped", type="boolean", nullable=false, options={"default": false})
     */
    private $equipped;

    /**
     * Set object
     *
     * @param object $object
     *
     * @return PlayerObject
     */
    public function setObject(object $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return PlayerObject
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return PlayerObject
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return PlayerObject
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Is equipped
     */
    public function isEquipped()
    {
        return $this->equipped;
    }

    /**
     * Get is equipped
     */
    public function getEquipped()
    {
        return $this->equipped;
    }

    /**
     * Set equipped
     *
     * @param string $equipped
     *
     * @return PlayerObject
     */
    public function setEquipped($equipped)
    {
        $this->equipped = (bool) $equipped;

        return $this;
    }

    /**
     * Can be Dropped
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("can_be_dropped")
     *
     * @return bool
     */
    public function canBeDropped()
    {
        return !in_array($this->getObject()->getType(), [Object::TYPE_UNIQUE]);
    }

    /**
     * Can be Dropped
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("can_be_equipped")
     *
     * @return bool
     */
    public function canBeEquipped()
    {
        return in_array(
            $this->getObject()->getType(),
            [
                Object::TYPE_VISION,
                Object::TYPE_WEAPON,
                Object::TYPE_SHIELD,
                Object::TYPE_ACCESSORY,
                Object::TYPE_CAP,
                Object::TYPE_SHOES,
                Object::TYPE_OUTFIT,
            ]
        );
    }

    /**
     * Can be used
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("can_be_used")
     *
     * @return bool
     */
    public function canBeUsed()
    {
        return in_array($this->getObject()->getType(), [Object::TYPE_CONSUMABLE]);
    }

    /**
     * Can use many of them
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("can_use_many")
     *
     * @return bool
     */
    public function canUseMany()
    {
        return in_array($this->getObject()->getType(), [Object::TYPE_CONSUMABLE]);
    }
}
