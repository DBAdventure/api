<?php

namespace Dba\GameBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;

/**
 * PlayerQuest
 *
 * @ORM\Table(name="player_quest")
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\PlayerQuestRepository")
 */
class PlayerQuest
{
    const STATUS_IN_PROGRESS = 0;
    const STATUS_FINISHED = 1;
    const STATUS_TIMEOUT = 2;

    /**
     * @var Player
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="playerQuests", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     * @JMS\Exclude
     */
    private $player;

    /**
     * @var Quest
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Quest")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="quest_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $quest;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = self::STATUS_IN_PROGRESS;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var array
     *
     * @ORM\Column(name="npc_objects", type="json_array", nullable=false)
     */
    private $npcObjects = [];

    /**
     * @var array
     *
     * @ORM\Column(name="npcs", type="json_array", nullable=false)
     */
    private $npcs = [];

    /**
     * Set quest
     *
     * @param Quest $quest
     *
     * @return PlayerQuest
     */
    public function setQuest(Quest $quest = null)
    {
        $this->quest = $quest;

        return $this;
    }

    /**
     * Get quest
     *
     * @return PlayerQuest
     */
    public function getQuest()
    {
        return $this->quest;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return PlayerQuest
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
     * Get npcs
     *
     * @return array
     */
    public function getNpcs()
    {
        return $this->npcs;
    }

    /**
     * Set npcs
     *
     * @param array $npcs
     *
     * @return PlayerQuest
     */
    public function setNpcs($npcs)
    {
        $this->npcs = $npcs;

        return $this;
    }

    /**
     * Get Npc objects
     *
     * @return array
     */
    public function getNpcObjects()
    {
        return $this->npcObjects;
    }

    /**
     * Set Npc objects
     *
     * @param array $npcObjects
     *
     * @return PlayerQuest
     */
    public function setNpcObjects($npcObjects)
    {
        $this->npcObjects = $npcObjects;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return Inbox
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Check if quest is in progress
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("is_in_progress")
     *
     * @return bool
     */
    public function isInProgress()
    {
        return $this->getStatus() === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if quest is finished
     *
     * @JMS\VirtualProperty
     * @JMS\SerializedName("is_finished")
     *
     * @return bool
     */
    public function isFinished()
    {
        return $this->getStatus() === self::STATUS_FINISHED;
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
