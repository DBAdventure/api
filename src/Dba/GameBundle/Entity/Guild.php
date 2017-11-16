<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Guild
 *
 * @ORM\Table(name="guild", uniqueConstraints={@ORM\UniqueConstraint(name="guild_short_name", columns={"short_name"}),
              @ORM\UniqueConstraint(name="guild_name", columns={"name"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\GuildRepository")
 */
class Guild
{
    const MAX_MEMBERS = 50;

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
     * @ORM\Column(name="short_name", type="string", length=5, nullable=false)
     * @Assert\NotBlank()
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="history", type="text")
     * @Assert\NotBlank()
     */
    private $history;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message = '';

    /**
     * @var string
     *
     * @ORM\Column(name="zeni", type="integer")
     */
    private $zeni = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default": false})
     */
    private $enabled = false;

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
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Player", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $createdBy;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dba\GameBundle\Entity\GuildPlayer", mappedBy="guild", cascade={"all"})
     * @JMS\Groups("GuildView")
     */
    private $players;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dba\GameBundle\Entity\GuildEvent", mappedBy="guild", cascade={"all"})
     * @ORM\OrderBy({"createdAt" = "DESC"})
     * @JMS\Exclude
     */
    private $events;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dba\GameBundle\Entity\GuildRank", mappedBy="guild", cascade={"all"})
     */
    private $ranks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->ranks = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Guild
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
     * Set short name
     *
     * @param string $shortName
     *
     * @return Guild
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get short name
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set history
     *
     * @param string $history
     *
     * @return Guild
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
     * Set message
     *
     * @param string $message
     *
     * @return Guild
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get zeni
     *
     * @return integer
     */
    public function getZeni()
    {
        return $this->zeni;
    }

    /**
     * Set zeni
     *
     * @param integer $zeni
     *
     * @return Guild
     */
    public function setZeni($zeni)
    {
        $this->zeni = $zeni;

        return $this;
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
     * Get created by
     *
     * @return Player
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set created by
     *
     * @param Player $createdBy
     *
     * @return Guild
     */
    public function setCreatedBy(Player $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
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
     * Add Player
     *
     * @param GuildPlayer $guildPlayer
     *
     * @return Guild
     */
    public function addPlayer(GuildPlayer $guildPlayer)
    {
        $this->players[] = $guildPlayer;

        return $this;
    }

    /**
     * Remove Player
     *
     * @param GuildPlayer $guildPlayer
     */
    public function removePlayer(GuildPlayer $guildPlayer)
    {
        $this->players->removeElement($guildPlayer);
    }

    /**
     * Get Players
     *
     * @return ArrayCollection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Add Event
     *
     * @param GuildEvent $guildEvent
     *
     * @return Guild
     */
    public function addEvent(GuildEvent $guildEvent)
    {
        $this->event[] = $guildEvent;

        return $this;
    }

    /**
     * Remove Event
     *
     * @param GuildEvent $guildEvent
     */
    public function removeEvent(GuildEvent $guildEvent)
    {
        $this->event->removeElement($guildEvent);
    }

    /**
     * Get Events
     *
     * @return ArrayCollection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Add Rank
     *
     * @param GuildRank $guildRank
     *
     * @return Guild
     */
    public function addRank(GuildRank $guildRank)
    {
        $this->ranks[] = $guildRank;

        return $this;
    }

    /**
     * Remove Rank
     *
     * @param GuildRank $guildRank
     */
    public function removeRank(GuildRank $guildRank)
    {
        if (in_array($guildRank->getRole(), [GuildRank::ROLE_MODO, GuildRank::ROLE_ADMIN])) {
            return;
        }

        $this->ranks->removeElement($guildRank);
    }

    /**
     * Get Ranks
     *
     * @return ArrayCollection
     */
    public function getRanks()
    {
        return $this->ranks;
    }

    /**
     * Check if user can archive
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("nb_members")
     *
     * @return integer
     */
    public function getNbMembers()
    {
        return count($this->getPlayers()->filter(
            function ($entity) {
                return $entity->isEnabled();
            }
        ));
    }

    /**
     * Check if user can archive
     *
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("nb_max_members")
     *
     * @return integer
     */
    public function getNbMaxMembers()
    {
        return self::MAX_MEMBERS;
    }
}
