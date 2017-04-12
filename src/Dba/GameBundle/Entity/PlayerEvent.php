<?php

namespace Dba\GameBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PlayerEvent
 *
 * @ORM\Table(name="player_event", indexes={@ORM\Index(name="player_event_player", columns={"player_id"}),
              @ORM\Index(name="player_event_target", columns={"target_id"})})
 * @ORM\Entity
 */
class PlayerEvent
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
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="playerEvents")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $player;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="targetEvents")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="target_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $target;

    /**
     * @var EventType
     *
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="event_type_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $eventType;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=80, nullable=false)
     */
    private $message;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="json_array", nullable=false)
     */
    private $parameters;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

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
     * Set event type
     *
     * @param EventType $eventType
     *
     * @return PlayerEvent
     */
    public function setEventType(EventType $eventType)
    {
        $this->eventType = $eventType;

        return $this;
    }

    /**
     * Get event type
     *
     * @return EventType
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * Set target
     *
     * @param Player $target
     *
     * @return PlayerEvent
     */
    public function setTarget(Player $target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return Player
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return PlayerEvent
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
     * Get parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set parameters
     *
     * @param string $parameters
     *
     * @return PlayerEvent
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return PlayerEvent
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get is createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     *
     * @return PlayerEvent
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
