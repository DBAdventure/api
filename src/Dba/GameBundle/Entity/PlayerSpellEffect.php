<?php

namespace Dba\GameBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * PlayerSpellEffect
 *
 * @ORM\Table(name="player_spell_effect",
 *     indexes={
 *         @ORM\Index(name="player_spell_effect_spell", columns={"player_spell_id"}),
 *         @ORM\Index(name="player_spell_effect_target_id", columns={"target_id"})
 *     },
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="player_spell_effect_target_player_spell",
 *         columns={"target_id", "player_spell_id"})
 *     })
 *     @ORM\Entity
 */
class PlayerSpellEffect
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
     * @ORM\ManyToOne(targetEntity="PlayerSpell", fetch="EAGER", inversedBy="playerSpellEffects")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="player_spell_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $playerSpell;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player", fetch="EAGER")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $target;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="times_used", type="integer", nullable=true)
     */
    private $timesUsed;

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
     * Set playerSpell
     *
     * @param Player $playerSpell
     *
     * @return PlayerSpellEffect
     */
    public function setPlayerSpell(PlayerSpell $playerSpell)
    {
        $this->playerSpell = $playerSpell;

        return $this;
    }

    /**
     * Get player spell
     *
     * @return PlayerSpell
     */
    public function getPlayerSpell()
    {
        return $this->playerSpell;
    }

    /**
     * Set target
     *
     * @param Player $target
     *
     * @return PlayerSpell
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
     * @return PlayerSpellEffect
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get times used
     *
     * @return int
     */
    public function getTimesUsed()
    {
        return $this->timesUsed;
    }

    /**
     * Set times used
     *
     * @param int $timesUsed
     *
     * @return PlayerSpellEffect
     */
    public function setTimesUsed($timesUsed)
    {
        $this->timesUsed = (int) $timesUsed;

        return $this;
    }
}
