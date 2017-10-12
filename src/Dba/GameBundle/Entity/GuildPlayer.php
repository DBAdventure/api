<?php

namespace Dba\GameBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * GuildPlayer
 *
 * @ORM\Table(name="guild_player", indexes={@ORM\Index(name="guild_player_rank", columns={"rank_id"}),
              @ORM\Index(name="guild_player_guild", columns={"guild_id"})},
              uniqueConstraints={@ORM\UniqueConstraint(name="guild_player_player", columns={"player_id"})})
 * @ORM\Entity
 */
class GuildPlayer
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
     * @ORM\OneToOne(targetEntity="Player", inversedBy="guildPlayer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     * @JMS\Groups({"Guild", "GuildView"})
     */
    private $player;

    /**
     * @var Guild
     *
     * @ORM\ManyToOne(targetEntity="Guild", inversedBy="players")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="guild_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $guild;

    /**
     * @var GuildRank
     *
     * @ORM\ManyToOne(targetEntity="GuildRank", cascade={"persist"}))
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rank_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $rank;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false, options={"default": false})
     */
    private $enabled = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="zeni", type="integer", nullable=false)
     */
    private $zeni;

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
     * Set guild
     *
     * @param Guild $guild
     *
     * @return GuildPlayer
     */
    public function setGuild(Guild $guild)
    {
        $this->guild = $guild;

        return $this;
    }

    /**
     * Get guild
     *
     * @return Guild
     */
    public function getGuild()
    {
        return $this->guild;
    }

    /**
     * Set rank
     *
     * @param GuildRank $rank
     *
     * @return GuildPlayer
     */
    public function setRank(GuildRank $rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return GuildRank
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return GuildPlayer
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
     * Get is enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
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
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return GuildPlayer
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
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
     * @return GuildPlayer
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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
     * @return GuildPlayer
     */
    public function setZeni($zeni)
    {
        $this->zeni = $zeni;

        return $this;
    }
}
