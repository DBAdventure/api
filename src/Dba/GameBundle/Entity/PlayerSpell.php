<?php

namespace Dba\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerSpell
 *
 * @ORM\Table(name="player_spell", indexes={
 *     @ORM\Index(name="player_spell_spell", columns={"spell_id"}),
 * @ORM\Index(name="player_spell_player", columns={"player_id"})},
 * uniqueConstraints={
 *     @ORM\UniqueConstraint(name="player_spell_player_spell",
 *     columns={"player_id", "spell_id"})
 * })
 * @ORM\Entity
 */
class PlayerSpell
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
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="playerSpells")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $player;

    /**
     * @var Spell
     *
     * @ORM\ManyToOne(targetEntity="Spell")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="spell_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $spell;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dba\GameBundle\Entity\PlayerSpellEffect", mappedBy="playerSpell", cascade={"all"})
     */
    private $playerSpellEffects;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->playerSpellEffects = new ArrayCollection();
    }

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
     * Set spell
     *
     * @param Spell $spell
     *
     * @return PlayerSpell
     */
    public function setSpell(Spell $spell)
    {
        $this->spell = $spell;

        return $this;
    }

    /**
     * Get spell
     *
     * @return Spell
     */
    public function getSpell()
    {
        return $this->spell;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return PlayerSpell
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
     * Add Player spell effect
     *
     * @param PlayerSpellEffect $playerSpellEffect
     *
     * @return PlayerSpell
     */
    public function addPlayerSpellEffect(PlayerSpellEffect $playerSpellEffect)
    {
        $this->playerSpellEffects[] = $playerSpellEffect;

        return $this;
    }

    /**
     * Remove Player spell effect
     *
     * @param PlayerSpellEffect $playerSpellEffect
     */
    public function removePlayerSpellEffect(PlayerSpellEffect $playerSpellEffect)
    {
        $this->playerSpellEffects->removeElement($playerSpellEffect);
    }

    /**
     * Get Player spell effects
     *
     * @return ArrayCollection
     */
    public function getPlayerSpellEffects()
    {
        return $this->playerSpellEffects;
    }

    /**
     * Check if spell can be used
     *
     * @return bool
     */
    public function canBeUsed()
    {
        $player = $this->getPlayer();
        $requirements = $this->getSpell()->getRequirements();

        return $player->getKi() >= $requirements['ki'] &&
               (empty($requirements['level']) || $player->getLevel() >= $requirements['level']) &&
               $player->getIntellect() >= $requirements['intellect'];
    }
}
