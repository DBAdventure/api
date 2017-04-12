<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Guild
 *
 * @ORM\Table(name="guild_rank", indexes={@ORM\Index(name="guild_rank_guild", columns={"guild_id"})},
              uniqueConstraints={@ORM\UniqueConstraint(name="guild_rank_name", columns={"name", "guild_id"})})
 * @ORM\Entity
 */
class GuildRank
{
    const ROLE_PLAYER = 'ROLE_PLAYER';
    const ROLE_MODO = 'ROLE_MODO';
    const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=80, nullable=false)
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     */
    private $name;

    /**
     * @var Guild
     *
     * @ORM\ManyToOne(targetEntity="Guild", inversedBy="ranks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="guild_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $guild;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return GuildPlayer
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Check if player is a moderator
     *
     * @return boolean
     */
    public function isModo()
    {
        return $this->getRole() == self::ROLE_MODO;
    }

    /**
     * Check if player is an administrator
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->getRole() == self::ROLE_ADMIN;
    }
}
