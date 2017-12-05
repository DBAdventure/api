<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * PlayerQuest
 *
 * @ORM\Table(name="player_quest")
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\PlayerQuestRepository")
 */
class PlayerQuest
{
    /**
     * @var Player
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="playerQuests", cascade={"persist", "remove"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="player_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $player;

    /**
     * @var Quest
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\ManyToOne(targetEntity="Quest", inversedBy="playerQuests")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quest_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $quest;

    /**
     * @var integer
     *
     * @ORM\Column(name="npc_objects", type="integer", nullable=false)
     */
    private $npcObjects;

    /**
     * @var integer
     *
     * @ORM\Column(name="npc", type="integer", nullable=false)
     */
    private $npc;

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
     * Get npc
     *
     * @return integer
     */
    public function getNpc()
    {
        return $this->npc;
    }

    /**
     * Set npc
     *
     * @param string $npc
     *
     * @return PlayerQuest
     */
    public function setNpc($npc)
    {
        $this->npc = $npc;

        return $this;
    }

    /**
     * Get Npc objects
     *
     * @return integer
     */
    public function getNpcObjects()
    {
        return $this->npcObjects;
    }

    /**
     * Set Npc objects
     *
     * @param integer $npcObjects
     *
     * @return PlayerQuest
     */
    public function setNpcObjects($npcObjects)
    {
        $this->npcObjects = $npcObjects;

        return $this;
    }
}
