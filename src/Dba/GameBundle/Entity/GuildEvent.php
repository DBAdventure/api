<?php

namespace Dba\GameBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * GuildEvent
 *
 * @ORM\Table(name="guild_event", indexes={@ORM\Index(name="guild_event_player", columns={"player_id"}),
 * @ORM\Index(name="guild_event_guild", columns={"guild_id"})})
 * @ORM\Entity
 */
class GuildEvent
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * })
     */
    private $player;

    /**
     * @var Guild
     *
     * @ORM\ManyToOne(targetEntity="Guild", inversedBy="events")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="guild_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $guild;

    /**
     * @var EventType
     *
     * @ORM\ManyToOne(targetEntity="EventType")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="event_type_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
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
     * @return int
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
     * @return GuildEvent
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
     * Set guild
     *
     * @param Guild $guild
     *
     * @return GuildEvent
     */
    public function setGuild(Guild $guild)
    {
        $this->guild = $guild;

        return $this;
    }

    /**
     * Get guild
     *
     * @return Player
     */
    public function getGuild()
    {
        return $this->guild;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return GuildEvent
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
     * @return GuildEvent
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
     * @return GuildEvent
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
     * @return GuildEvent
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
