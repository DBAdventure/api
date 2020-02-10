<?php

namespace Dba\GameBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Player
 *
 * @ORM\Table(name="player", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="player_username", columns={"username"}),
 *     @ORM\UniqueConstraint(name="player_email", columns={"email"}),
 *     @ORM\UniqueConstraint(name="player_username_canonical", columns={"username_canonical"}),
 *     @ORM\UniqueConstraint(name="player_email_canonical", columns={"email_canonical"}),
 *     @ORM\UniqueConstraint(name="player_name", columns={"name"}),
 *     @ORM\UniqueConstraint(name="player_confirmation_token", columns={"confirmation_token"})
 * },
 * indexes={
 *     @ORM\Index(name="player_race_id", columns={"race_id"}),
 *     @ORM\Index(name="player_map_id", columns={"map_id"}),
 *     @ORM\Index(name="player_rank_id", columns={"rank_id"}),
 *     @ORM\Index(name="player_side_id", columns={"side_id"}),
 *     @ORM\Index(name="player_target_id", columns={"target_id"})
 * })
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\PlayerRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 * @ORM\HasLifecycleCallbacks
 */
class Player implements AdvancedUserInterface, Serializable
{
    const TIME_ACTION_POINT = 5;
    const TIME_KI_POINT = 10;
    const TIME_FATIGUE_POINT = 5;
    const TIME_MOVEMENT_POINT = 3;

    const MAX_ACTION_POINTS = 100;
    const MAX_MOVEMENT_POINTS = 150;
    const MAX_FATIGUE_POINTS = 200;

    const ATTACK_TYPE_BETRAY = 'betray';
    const ATTACK_TYPE_REVENGE = 'revenge';
    const ATTACK_TYPE_SLAP = 'slap';

    const HEALTH_POINT = 'HEALTH';
    const KI_POINT = 'KI';
    const ACTION_POINT = 'AP';
    const MOVEMENT_POINT = 'MP';
    const FATIGUE_POINT = 'FP';
    const SKILL_POINT = 'SK';

    const SLAP_ACTION = 0;
    const PICKUP_ACTION = 1;
    const GIVE_ACTION = 2;
    const STEAL_ACTION = 3;
    const ANALYSIS_ACTION = 3;
    const HEAL_ACTION = 4;
    const ATTACK_ACTION = 5;
    const SPELL_ACTION = 6;
    const MOVEMENT_ACTION = 5;

    const ROLE_PLAYER = 'ROLE_PLAYER';
    const ROLE_MODO = 'ROLE_MODO';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    const AVAILABLE_MOVE = ['n', 'ne', 'e', 'se', 's', 'sw', 'w', 'nw'];

    /**
     * Cache for specifications such as objects strength, vision,
     * resistance, and others bonus
     *
     * @var array
     * @JMS\Exclude
     */
    protected $specifications = [];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @JMS\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username_canonical", type="string", length=180, nullable=false, unique=true)
     * @JMS\Exclude
     */
    private $usernameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=180, nullable=false, unique=true)
     * @Assert\NotBlank
     * @JMS\Exclude
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email_canonical", type="string", length=180, nullable=false, unique=true)
     * @JMS\Exclude
     */
    private $emailCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=180, nullable=false, unique=true)
     * @Assert\NotBlank
     * @Assert\Email
     * @JMS\Exclude
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="enabled", type="boolean", options={"default": false})
     * @JMS\Exclude
     */
    private $enabled = false;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", nullable=true)
     * @JMS\Exclude
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string")
     * @JMS\Exclude
     */
    private $password;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     * @JMS\Expose
     */
    private $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string", length=180, nullable=true, unique=true)
     * @JMS\Exclude
     */
    private $confirmationToken;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     * @JMS\Exclude
     */
    private $passwordRequestedAt;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json_array")
     * @JMS\Exclude
     */
    private $roles = [];

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min=3,
     *     max=50
     * )
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="history", type="text", nullable=true)
     * @JMS\Expose
     */
    private $history;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=10, nullable=false)
     * @Assert\NotBlank
     * @JMS\Expose
     */
    private $image;

    /**
     * @var int
     *
     * @ORM\Column(name="zeni", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $zeni = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="integer", nullable=false)
     * @JMS\Expose
     */
    private $level = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="accuracy", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $accuracy;

    /**
     * @var int
     *
     * @ORM\Column(name="agility", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $agility;

    /**
     * @var int
     *
     * @ORM\Column(name="strength", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $strength;

    /**
     * @var int
     *
     * @ORM\Column(name="resistance", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $resistance;

    /**
     * @var int
     *
     * @ORM\Column(name="skill", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $skill;

    /**
     * @var int
     *
     * @ORM\Column(name="vision", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $vision;

    /**
     * @var int
     *
     * @ORM\Column(name="analysis", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $analysis;

    /**
     * @var int
     *
     * @ORM\Column(name="intellect", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $intellect;

    /**
     * @var int
     *
     * @ORM\Column(name="ki", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $ki;

    /**
     * @var int
     *
     * @ORM\Column(name="max_ki", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $maxKi;

    /**
     * @var int
     *
     * @ORM\Column(name="health", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $health;

    /**
     * @var int
     *
     * @ORM\Column(name="max_health", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $maxHealth;

    /**
     * @var int
     *
     * @ORM\Column(name="side_points", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $sidePoints;

    /**
     * @var int
     *
     * @ORM\Column(name="action_points", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $actionPoints;

    /**
     * @var int
     *
     * @ORM\Column(name="fatigue_points", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $fatiguePoints;

    /**
     * @var int
     *
     * @ORM\Column(name="movement_points", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $movementPoints;

    /**
     * @var int
     *
     * @ORM\Column(name="battle_points", type="integer", nullable=false)
     * @JMS\Exclude
     */
    private $battlePoints;

    /**
     * @var int
     *
     * @ORM\Column(name="skill_points", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $skillPoints = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15, nullable=false)
     * @JMS\Exclude
     */
    private $ip;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     * @JMS\Exclude
     */
    private $createdAt;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     * @JMS\Exclude
     */
    private $updatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="action_updated_at", type="datetime", nullable=false)
     * @JMS\Exclude
     */
    private $actionUpdatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="movement_updated_at", type="datetime", nullable=false)
     * @JMS\Exclude
     */
    private $movementUpdatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="ki_updated_at", type="datetime", nullable=false)
     * @JMS\Exclude
     */
    private $kiUpdatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="fatigue_updated_at", type="datetime", nullable=false)
     * @JMS\Exclude
     */
    private $fatigueUpdatedAt;

    /**
     * @var int
     *
     * @ORM\Column(name="x", type="integer", nullable=false)
     * @JMS\Groups("Guild")
     */
    private $x;

    /**
     * @var int
     *
     * @ORM\Column(name="y", type="integer", nullable=false)
     * @JMS\Groups("Guild")
     */
    private $y;

    /**
     * @var string
     *
     * @ORM\Column(name="forbidden_teleport", type="string", length=2, nullable=true)
     * @JMS\Exclude
     */
    private $forbiddenTeleport;

    /**
     * @var int
     *
     * @ORM\Column(name="death_count", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $deathCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_kill_good", type="integer", nullable=false, options={"default": 0})
     * @JMS\Expose
     */
    private $nbKillGood = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_hit_good", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbHitGood = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_damage_good", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbDamageGood = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_kill_bad", type="integer", nullable=false, options={"default": 0})
     * @JMS\Expose
     */
    private $nbKillBad = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_hit_bad", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbHitBad = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_damage_bad", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbDamageBad = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_kill_npc", type="integer", nullable=false, options={"default": 0})
     * @JMS\Expose
     */
    private $nbKillNpc = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_hit_npc", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbHitNpc = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_damage_npc", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbDamageNpc = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_kill_hq", type="integer", nullable=false, options={"default": 0})
     * @JMS\Expose
     */
    private $nbKillHq = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_hit_hq", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbHitHq = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_damage_hq", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbDamageHq = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_stolen_zeni", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbStolenZeni = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_action_stolen_zeni", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbActionStolenZeni = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_dodge", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbDodge = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_wanted", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbWanted = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_analysis", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbAnalysis = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_spell", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbSpell = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_health_given", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbHealthGiven = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_total_health_given", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbTotalHealthGiven = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_slap_taken", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbSlapTaken = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_slap_given", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $nbSlapGiven = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="betrayals", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $betrayals = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="head_price", type="integer", nullable=false, options={"default": 0})
     * @JMS\Exclude
     */
    private $headPrice = 0;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Player", fetch="EAGER")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @JMS\Exclude
     */
    private $target;

    /**
     * @var Side
     *
     * @ORM\ManyToOne(targetEntity="Side", fetch="EAGER")
     * @ORM\JoinColumn(name="side_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $side;

    /**
     * @var Rank
     *
     * @ORM\ManyToOne(targetEntity="Rank", fetch="EAGER")
     * @ORM\JoinColumn(name="rank_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $rank;

    /**
     * @var Race
     *
     * @ORM\ManyToOne(targetEntity="Race", fetch="EAGER")
     * @ORM\JoinColumn(name="race_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     */
    private $race;

    /**
     * @var Map
     *
     * @ORM\ManyToOne(targetEntity="Map", fetch="EAGER", cascade={"persist"})
     * @ORM\JoinColumn(name="map_id", referencedColumnName="id", nullable=false)
     * @JMS\Groups("Guild")
     */
    private $map;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PlayerObject", mappedBy="player", cascade={"persist"})
     * @JMS\Exclude
     */
    private $playerObjects = [];

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PlayerSpell", mappedBy="player", cascade={"persist"})
     * @JMS\Exclude
     */
    private $playerSpells = [];

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="PlayerQuest", mappedBy="player", cascade={"persist"})
     * @JMS\Exclude
     */
    private $playerQuests = [];

    /**
     * @var GuildPlayer
     *
     * @ORM\OneToOne(targetEntity="GuildPlayer", mappedBy="player", cascade={"persist"})
     * @JMS\Expose
     */
    private $guildPlayer;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->playerObjects = new ArrayCollection();
        $this->playerQuests = new ArrayCollection();
        $this->playerSpells = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Player
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
     * Set history
     *
     * @param string $history
     *
     * @return Player
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
     * Set image
     *
     * @param string $image
     *
     * @return Player
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set zeni
     *
     * @param int $zeni
     *
     * @return Player
     */
    public function setZeni($zeni)
    {
        $this->zeni = $zeni;

        return $this;
    }

    /**
     * Get zeni
     *
     * @return int
     */
    public function getZeni()
    {
        return $this->zeni;
    }

    /**
     * Set level
     *
     * @param int $level
     *
     * @return Player
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set accuracy
     *
     * @param int $accuracy
     *
     * @return Player
     */
    public function setAccuracy($accuracy)
    {
        $this->accuracy = $accuracy;

        return $this;
    }

    /**
     * Get accuracy
     *
     * @return int
     */
    public function getAccuracy()
    {
        return $this->accuracy;
    }

    /**
     * Get total accuracy
     *
     * @return int
     */
    public function getTotalAccuracy()
    {
        return $this->specifications['total']['accuracy'];
    }

    /**
     * Get objects accuracy
     *
     * @return int
     */
    public function getObjectsAccuracy()
    {
        return $this->specifications['objects']['accuracy'];
    }

    /**
     * Set agility
     *
     * @param int $agility
     *
     * @return Player
     */
    public function setAgility($agility)
    {
        $this->agility = $agility;

        return $this;
    }

    /**
     * Get agility
     *
     * @return int
     */
    public function getAgility()
    {
        return $this->agility;
    }

    /**
     * Get total agility
     *
     * @return int
     */
    public function getTotalAgility()
    {
        return $this->specifications['total']['agility'];
    }

    /**
     * Get objects agility
     *
     * @return int
     */
    public function getObjectsAgility()
    {
        return $this->specifications['objects']['agility'];
    }

    /**
     * Set strength
     *
     * @param int $strength
     *
     * @return Player
     */
    public function setStrength($strength)
    {
        $this->strength = $strength;

        return $this;
    }

    /**
     * Get strength
     *
     * @return int
     */
    public function getStrength()
    {
        return $this->strength;
    }

    /**
     * Get total strength
     *
     * @return int
     */
    public function getTotalStrength()
    {
        return $this->specifications['total']['strength'];
    }

    /**
     * Get objects strength
     *
     * @return int
     */
    public function getObjectsStrength()
    {
        return $this->specifications['objects']['strength'];
    }

    /**
     * Set resistance
     *
     * @param int $resistance
     *
     * @return Player
     */
    public function setResistance($resistance)
    {
        $this->resistance = $resistance;

        return $this;
    }

    /**
     * Get resistance
     *
     * @return int
     */
    public function getResistance()
    {
        return $this->resistance;
    }

    /**
     * Get total resistance
     *
     * @return int
     */
    public function getTotalResistance()
    {
        return $this->specifications['total']['resistance'];
    }

    /**
     * Get objects resistance
     *
     * @return int
     */
    public function getObjectsResistance()
    {
        return $this->specifications['objects']['resistance'];
    }

    /**
     * Set skill
     *
     * @param int $skill
     *
     * @return Player
     */
    public function setSkill($skill)
    {
        $this->skill = $skill;

        return $this;
    }

    /**
     * Get skill
     *
     * @return int
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * Get total skill
     *
     * @return int
     */
    public function getTotalSkill()
    {
        return $this->specifications['total']['skill'];
    }

    /**
     * Get objects skill
     *
     * @return int
     */
    public function getObjectsSkill()
    {
        return $this->specifications['objects']['skill'];
    }

    /**
     * Set vision
     *
     * @param int $vision
     *
     * @return Player
     */
    public function setVision($vision)
    {
        $this->vision = $vision;

        return $this;
    }

    /**
     * Get vision
     *
     * @return int
     */
    public function getVision()
    {
        return $this->vision;
    }

    /**
     * Get total vision
     *
     * @return int
     */
    public function getTotalVision()
    {
        return $this->specifications['total']['vision'];
    }

    /**
     * Get objects vision
     *
     * @return int
     */
    public function getObjectsVision()
    {
        return $this->specifications['objects']['vision'];
    }

    /**
     * Set analysis
     *
     * @param int $analysis
     *
     * @return Player
     */
    public function setAnalysis($analysis)
    {
        $this->analysis = $analysis;

        return $this;
    }

    /**
     * Get analysis
     *
     * @return int
     */
    public function getAnalysis()
    {
        return $this->analysis;
    }

    /**
     * Get total analysis
     *
     * @return int
     */
    public function getTotalAnalysis()
    {
        return $this->specifications['total']['analysis'];
    }

    /**
     * Get objects analysis
     *
     * @return int
     */
    public function getObjectsAnalysis()
    {
        return $this->specifications['objects']['analysis'];
    }

    /**
     * Set intellect
     *
     * @param int $intellect
     *
     * @return Player
     */
    public function setIntellect($intellect)
    {
        $this->intellect = $intellect;

        return $this;
    }

    /**
     * Get intellect
     *
     * @return int
     */
    public function getIntellect()
    {
        return $this->intellect;
    }

    /**
     * Get total intellect
     *
     * @return int
     */
    public function getTotalIntellect()
    {
        return $this->specifications['total']['intellect'];
    }

    /**
     * Get objects intellect
     *
     * @return int
     */
    public function getObjectsIntellect()
    {
        return $this->specifications['objects']['intellect'];
    }

    /**
     * Set ki
     *
     * @param int $ki
     *
     * @return Player
     */
    public function setKi($ki)
    {
        $this->ki = $ki;

        return $this;
    }

    /**
     * Get ki
     *
     * @return int
     */
    public function getKi()
    {
        return $this->ki;
    }

    /**
     * Set maxKi
     *
     * @param int $maxKi
     *
     * @return Player
     */
    public function setMaxKi($maxKi)
    {
        $this->maxKi = $maxKi;

        return $this;
    }

    /**
     * Get maxKi
     *
     * @return int
     */
    public function getMaxKi()
    {
        return $this->maxKi;
    }

    /**
     * Get total maxKi
     *
     * @return int
     */
    public function getTotalMaxKi()
    {
        return $this->specifications['total']['max_ki'];
    }

    /**
     * Get objects maxKi
     *
     * @return int
     */
    public function getObjectsMaxKi()
    {
        return $this->specifications['objects']['max_ki'];
    }

    /**
     * Set health
     *
     * @param int $health
     *
     * @return Player
     */
    public function setHealth($health)
    {
        $this->health = $health;

        return $this;
    }

    /**
     * Get health
     *
     * @return int
     */
    public function getHealth()
    {
        return $this->health;
    }

    /**
     * Set maxHealth
     *
     * @param int $maxHealth
     *
     * @return Player
     */
    public function setMaxHealth($maxHealth)
    {
        $this->maxHealth = $maxHealth;

        return $this;
    }

    /**
     * Get maxHealth
     *
     * @return int
     */
    public function getMaxHealth()
    {
        return $this->maxHealth;
    }

    /**
     * Get total maxHealth
     *
     * @return int
     */
    public function getTotalMaxHealth()
    {
        return $this->specifications['total']['max_health'];
    }

    /**
     * Get objects maxHealth
     *
     * @return int
     */
    public function getObjectsMaxHealth()
    {
        return $this->specifications['objects']['max_health'];
    }

    /**
     * Set actionPoints
     *
     * @param int $actionPoints
     *
     * @return Player
     */
    public function setActionPoints($actionPoints)
    {
        $this->actionPoints = $actionPoints;

        return $this;
    }

    /**
     * Get actionPoints
     *
     * @return int
     */
    public function getActionPoints()
    {
        return $this->actionPoints;
    }

    /**
     * Get maxActionPoints
     *
     * @return int
     */
    public function getMaxActionPoints()
    {
        return self::MAX_ACTION_POINTS;
    }

    /**
     * Set fatiguePoints
     *
     * @param int $fatiguePoints
     *
     * @return Player
     */
    public function setFatiguePoints($fatiguePoints)
    {
        $this->fatiguePoints = $fatiguePoints;

        return $this;
    }

    /**
     * Get fatiguePoints
     *
     * @return int
     */
    public function getFatiguePoints()
    {
        return $this->fatiguePoints;
    }

    /**
     * Get maxFatiguePoints
     *
     * @return int
     */
    public function getMaxFatiguePoints()
    {
        return self::MAX_FATIGUE_POINTS;
    }

    /**
     * Set movementPoints
     *
     * @param int $movementPoints
     *
     * @return Player
     */
    public function setMovementPoints($movementPoints)
    {
        $this->movementPoints = $movementPoints;

        return $this;
    }

    /**
     * Get movementPoints
     *
     * @return int
     */
    public function getMovementPoints()
    {
        return $this->movementPoints;
    }

    /**
     * Get maxMovementPoints
     *
     * @return int
     */
    public function getMaxMovementPoints()
    {
        return self::MAX_MOVEMENT_POINTS;
    }

    /**
     * Set battlePoints
     *
     * @param int $battlePoints
     *
     * @return Player
     */
    public function setBattlePoints($battlePoints)
    {
        $this->battlePoints = $battlePoints;

        return $this;
    }

    /**
     * Get battlePoints
     *
     * @return int
     */
    public function getBattlePoints()
    {
        return $this->battlePoints;
    }

    /**
     * Set ip
     *
     * @param int $ip
     *
     * @return Player
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return int
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     *
     * @return Player
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

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
     * Set updatedAt
     *
     * @param DateTime $updatedAt
     *
     * @return Player
     */
    public function setUpdatedAt(DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
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
     * Set side
     *
     * @param Side $side
     *
     * @return Player
     */
    public function setSide(Side $side = null)
    {
        $this->side = $side;

        return $this;
    }

    /**
     * Get side
     *
     * @return Side
     */
    public function getSide()
    {
        return $this->side;
    }

    /**
     * Set rank
     *
     * @param Rank $rank
     *
     * @return Player
     */
    public function setRank(Rank $rank = null)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return Rank
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set race
     *
     * @param Race $race
     *
     * @return Player
     */
    public function setRace(Race $race = null)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get race
     *
     * @return Race
     */
    public function getRace()
    {
        return $this->race;
    }

    /**
     * Set map
     *
     * @param Map $map
     *
     * @return Player
     */
    public function setMap(Map $map = null)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get map
     *
     * @return Map
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * Set X
     *
     * @param int $x
     *
     * @return Player
     */
    public function setX($x)
    {
        $this->x = $x;

        return $this;
    }

    /**
     * Get x
     *
     * @return int
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Set Y
     *
     * @param int $y
     *
     * @return Player
     */
    public function setY($y)
    {
        $this->y = $y;

        return $this;
    }

    /**
     * Get y
     *
     * @return int
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Set forbiddenTeleport
     *
     * @param string $forbiddenTeleport
     *
     * @return Player
     */
    public function setForbiddenTeleport($forbiddenTeleport)
    {
        $this->forbiddenTeleport = $forbiddenTeleport;

        return $this;
    }

    /**
     * Get forbiddenTeleport
     *
     * @return string
     */
    public function getForbiddenTeleport()
    {
        return $this->forbiddenTeleport;
    }

    /**
     * Set sidePoints
     *
     * @param int $sidePoints
     *
     * @return Player
     */
    public function setSidePoints($sidePoints)
    {
        $this->sidePoints = $sidePoints;

        return $this;
    }

    /**
     * Get sidePoints
     *
     * @return int
     */
    public function getSidePoints()
    {
        return $this->sidePoints;
    }

    /**
     * Set skillPoints
     *
     * @param int $skillPoints
     *
     * @return Player
     */
    public function setSkillPoints($skillPoints)
    {
        $this->skillPoints = $skillPoints;

        return $this;
    }

    /**
     * Get skillPoints
     *
     * @return int
     */
    public function getSkillPoints()
    {
        return $this->skillPoints;
    }

    /**
     * Set actionUpdatedAt
     *
     * @param DateTime $actionUpdatedAt
     *
     * @return Player
     */
    public function setActionUpdatedAt(DateTime $actionUpdatedAt)
    {
        $this->actionUpdatedAt = $actionUpdatedAt;

        return $this;
    }

    /**
     * Get actionUpdatedAt
     *
     * @return DateTime
     */
    public function getActionUpdatedAt()
    {
        return $this->actionUpdatedAt;
    }

    /**
     * Set movementUpdatedAt
     *
     * @param DateTime $movementUpdatedAt
     *
     * @return Player
     */
    public function setMovementUpdatedAt(DateTime $movementUpdatedAt)
    {
        $this->movementUpdatedAt = $movementUpdatedAt;

        return $this;
    }

    /**
     * Get movementUpdatedAt
     *
     * @return DateTime
     */
    public function getMovementUpdatedAt()
    {
        return $this->movementUpdatedAt;
    }

    /**
     * Set kiUpdatedAt
     *
     * @param DateTime $kiUpdatedAt
     *
     * @return Player
     */
    public function setKiUpdatedAt(DateTime $kiUpdatedAt)
    {
        $this->kiUpdatedAt = $kiUpdatedAt;

        return $this;
    }

    /**
     * Get kiUpdatedAt
     *
     * @return DateTime
     */
    public function getKiUpdatedAt()
    {
        return $this->kiUpdatedAt;
    }

    /**
     * Set fatigueUpdatedAt
     *
     * @param DateTime $fatigueUpdatedAt
     *
     * @return Player
     */
    public function setFatigueUpdatedAt(DateTime $fatigueUpdatedAt)
    {
        $this->fatigueUpdatedAt = $fatigueUpdatedAt;

        return $this;
    }

    /**
     * Get fatigueUpdatedAt
     *
     * @return DateTime
     */
    public function getFatigueUpdatedAt()
    {
        return $this->fatigueUpdatedAt;
    }

    /**
     * Set deathCount
     *
     * @param int $deathCount
     *
     * @return Player
     */
    public function setDeathCount($deathCount)
    {
        $this->deathCount = $deathCount;

        return $this;
    }

    /**
     * Get deathCount
     *
     * @return int
     */
    public function getDeathCount()
    {
        return $this->deathCount;
    }

    /**
     * Set nbKillGood
     *
     * @param int $nbKillGood
     *
     * @return Player
     */
    public function setNbKillGood($nbKillGood)
    {
        $this->nbKillGood = $nbKillGood;

        return $this;
    }

    /**
     * Get nbKillGood
     *
     * @return int
     */
    public function getNbKillGood()
    {
        return $this->nbKillGood;
    }

    /**
     * Set nbHitGood
     *
     * @param int $nbHitGood
     *
     * @return Player
     */
    public function setNbHitGood($nbHitGood)
    {
        $this->nbHitGood = $nbHitGood;

        return $this;
    }

    /**
     * Get nbHitGood
     *
     * @return int
     */
    public function getNbHitGood()
    {
        return $this->nbHitGood;
    }

    /**
     * Set nbDamageGood
     *
     * @param int $nbDamageGood
     *
     * @return Player
     */
    public function setNbDamageGood($nbDamageGood)
    {
        $this->nbDamageGood = $nbDamageGood;

        return $this;
    }

    /**
     * Get nbDamageGood
     *
     * @return int
     */
    public function getNbDamageGood()
    {
        return $this->nbDamageGood;
    }

    /**
     * Set nbKillBad
     *
     * @param int $nbKillBad
     *
     * @return Player
     */
    public function setNbKillBad($nbKillBad)
    {
        $this->nbKillBad = $nbKillBad;

        return $this;
    }

    /**
     * Get nbKillBad
     *
     * @return int
     */
    public function getNbKillBad()
    {
        return $this->nbKillBad;
    }

    /**
     * Set nbHitBad
     *
     * @param int $nbHitBad
     *
     * @return Player
     */
    public function setNbHitBad($nbHitBad)
    {
        $this->nbHitBad = $nbHitBad;

        return $this;
    }

    /**
     * Get nbHitBad
     *
     * @return int
     */
    public function getNbHitBad()
    {
        return $this->nbHitBad;
    }

    /**
     * Set nbDamageBad
     *
     * @param int $nbDamageBad
     *
     * @return Player
     */
    public function setNbDamageBad($nbDamageBad)
    {
        $this->nbDamageBad = $nbDamageBad;

        return $this;
    }

    /**
     * Get nbDamageBad
     *
     * @return int
     */
    public function getNbDamageBad()
    {
        return $this->nbDamageBad;
    }

    /**
     * Set nbKillNpc
     *
     * @param int $nbKillNpc
     *
     * @return Player
     */
    public function setNbKillNpc($nbKillNpc)
    {
        $this->nbKillNpc = $nbKillNpc;

        return $this;
    }

    /**
     * Get nbKillNpc
     *
     * @return int
     */
    public function getNbKillNpc()
    {
        return $this->nbKillNpc;
    }

    /**
     * Set nbHitNpc
     *
     * @param int $nbHitNpc
     *
     * @return Player
     */
    public function setNbHitNpc($nbHitNpc)
    {
        $this->nbHitNpc = $nbHitNpc;

        return $this;
    }

    /**
     * Get nbHitNpc
     *
     * @return int
     */
    public function getNbHitNpc()
    {
        return $this->nbHitNpc;
    }

    /**
     * Set nbDamageNpc
     *
     * @param int $nbDamageNpc
     *
     * @return Player
     */
    public function setNbDamageNpc($nbDamageNpc)
    {
        $this->nbDamageNpc = $nbDamageNpc;

        return $this;
    }

    /**
     * Get nbDamageNpc
     *
     * @return int
     */
    public function getNbDamageNpc()
    {
        return $this->nbDamageNpc;
    }

    /**
     * Set nbKillHq
     *
     * @param int $nbKillHq
     *
     * @return Player
     */
    public function setNbKillHq($nbKillHq)
    {
        $this->nbKillHq = $nbKillHq;

        return $this;
    }

    /**
     * Get nbKillHq
     *
     * @return int
     */
    public function getNbKillHq()
    {
        return $this->nbKillHq;
    }

    /**
     * Set nbHitHq
     *
     * @param int $nbHitHq
     *
     * @return Player
     */
    public function setNbHitHq($nbHitHq)
    {
        $this->nbHitHq = $nbHitHq;

        return $this;
    }

    /**
     * Get nbHitHq
     *
     * @return int
     */
    public function getNbHitHq()
    {
        return $this->nbHitHq;
    }

    /**
     * Set nbDamageHq
     *
     * @param int $nbDamageHq
     *
     * @return Player
     */
    public function setNbDamageHq($nbDamageHq)
    {
        $this->nbDamageHq = $nbDamageHq;

        return $this;
    }

    /**
     * Get nbDamageHq
     *
     * @return int
     */
    public function getNbDamageHq()
    {
        return $this->nbDamageHq;
    }

    /**
     * Set nbStolenZeni
     *
     * @param int $nbStolenZeni
     *
     * @return Player
     */
    public function setNbStolenZeni($nbStolenZeni)
    {
        $this->nbStolenZeni = $nbStolenZeni;

        return $this;
    }

    /**
     * Get nbStolenZeni
     *
     * @return int
     */
    public function getNbStolenZeni()
    {
        return $this->nbStolenZeni;
    }

    /**
     * Set nbActionStolenZeni
     *
     * @param int $nbActionStolenZeni
     *
     * @return Player
     */
    public function setNbActionStolenZeni($nbActionStolenZeni)
    {
        $this->nbActionStolenZeni = $nbActionStolenZeni;

        return $this;
    }

    /**
     * Get nbActionStolenZeni
     *
     * @return int
     */
    public function getNbActionStolenZeni()
    {
        return $this->nbActionStolenZeni;
    }

    /**
     * Set nbDodge
     *
     * @param int $nbDodge
     *
     * @return Player
     */
    public function setNbDodge($nbDodge)
    {
        $this->nbDodge = $nbDodge;

        return $this;
    }

    /**
     * Get nbDodge
     *
     * @return int
     */
    public function getNbDodge()
    {
        return $this->nbDodge;
    }

    /**
     * Set nbWanted
     *
     * @param int $nbWanted
     *
     * @return Player
     */
    public function setNbWanted($nbWanted)
    {
        $this->nbWanted = $nbWanted;

        return $this;
    }

    /**
     * Get nbWanted
     *
     * @return int
     */
    public function getNbWanted()
    {
        return $this->nbWanted;
    }

    /**
     * Set nbAnalysis
     *
     * @param int $nbAnalysis
     *
     * @return Player
     */
    public function setNbAnalysis($nbAnalysis)
    {
        $this->nbAnalysis = $nbAnalysis;

        return $this;
    }

    /**
     * Get nbAnalysis
     *
     * @return int
     */
    public function getNbAnalysis()
    {
        return $this->nbAnalysis;
    }

    /**
     * Set nbSpell
     *
     * @param int $nbSpell
     *
     * @return Player
     */
    public function setNbSpell($nbSpell)
    {
        $this->nbSpell = $nbSpell;

        return $this;
    }

    /**
     * Get nbSpell
     *
     * @return int
     */
    public function getNbSpell()
    {
        return $this->nbSpell;
    }

    /**
     * Set nbHealthGiven
     *
     * @param int $nbHealthGiven
     *
     * @return Player
     */
    public function setNbHealthGiven($nbHealthGiven)
    {
        $this->nbHealthGiven = $nbHealthGiven;

        return $this;
    }

    /**
     * Get nbHealthGiven
     *
     * @return int
     */
    public function getNbHealthGiven()
    {
        return $this->nbHealthGiven;
    }

    /**
     * Set nbTotalHealthGiven
     *
     * @param int $nbTotalHealthGiven
     *
     * @return Player
     */
    public function setNbTotalHealthGiven($nbTotalHealthGiven)
    {
        $this->nbTotalHealthGiven = $nbTotalHealthGiven;

        return $this;
    }

    /**
     * Get nbTotalHealthGiven
     *
     * @return int
     */
    public function getNbTotalHealthGiven()
    {
        return $this->nbTotalHealthGiven;
    }

    /**
     * Set nbSlapTaken
     *
     * @param int $nbSlapTaken
     *
     * @return Player
     */
    public function setNbSlapTaken($nbSlapTaken)
    {
        $this->nbSlapTaken = $nbSlapTaken;

        return $this;
    }

    /**
     * Get nbSlapTaken
     *
     * @return int
     */
    public function getNbSlapTaken()
    {
        return $this->nbSlapTaken;
    }

    /**
     * Set nbSlapGiven
     *
     * @param int $nbSlapGiven
     *
     * @return Player
     */
    public function setNbSlapGiven($nbSlapGiven)
    {
        $this->nbSlapGiven = $nbSlapGiven;

        return $this;
    }

    /**
     * Get nbSlapGiven
     *
     * @return int
     */
    public function getNbSlapGiven()
    {
        return $this->nbSlapGiven;
    }

    /**
     * Set betrayals
     *
     * @param int $betrayals
     *
     * @return Player
     */
    public function setBetrayals($betrayals)
    {
        $this->betrayals = $betrayals;

        return $this;
    }

    /**
     * Get betrayals
     *
     * @return int
     */
    public function getBetrayals()
    {
        return $this->betrayals;
    }

    /**
     * Set head price
     *
     * @param int $headPrice
     *
     * @return Player
     */
    public function setHeadPrice($headPrice)
    {
        $this->headPrice = $headPrice;

        return $this;
    }

    /**
     * Get head price
     *
     * @return int
     */
    public function getHeadPrice()
    {
        return $this->headPrice;
    }

    /**
     * Set target
     *
     * @param Player $target
     *
     * @return Player
     */
    public function setTarget(Player $target = null)
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
     * Add player object
     *
     * @param PlayerObject $playerObject
     *
     * @return Player
     */
    public function addPlayerObject(PlayerObject $playerObject)
    {
        $this->playerObjects[] = $playerObject;

        return $this;
    }

    /**
     * Remove player object
     *
     * @param PlayerObject $playerObject
     */
    public function removePlayerObject(PlayerObject $playerObject)
    {
        $this->playerObjects->removeElement($playerObject);
    }

    /**
     * Get player object
     *
     * @return ArrayCollection
     */
    public function getPlayerObjects()
    {
        return $this->playerObjects;
    }

    /**
     * Add player quest
     *
     * @param PlayerQuest $playerQuest
     *
     * @return Player
     */
    public function addPlayerQuest(PlayerQuest $playerQuest)
    {
        $this->playerQuests[] = $playerQuest;

        return $this;
    }

    /**
     * Remove player quest
     *
     * @param PlayerQuest $playerQuest
     */
    public function removePlayerQuest(PlayerQuest $playerQuest)
    {
        $this->playerQuests->removeElement($playerQuest);
    }

    /**
     * Get player quests
     *
     * @return ArrayCollection
     */
    public function getPlayerQuests()
    {
        return $this->playerQuests;
    }

    /**
     * Add player spell
     *
     * @param PlayerSpell $playerSpell
     *
     * @return Player
     */
    public function addPlayerSpell(PlayerSpell $playerSpell)
    {
        $this->playerSpells[] = $playerSpell;

        return $this;
    }

    /**
     * Remove player spell
     *
     * @param PlayerSpell $playerSpell
     */
    public function removePlayerSpell(PlayerSpell $playerSpell)
    {
        $this->playerSpells->removeElement($playerSpell);
    }

    /**
     * Get player spell
     *
     * @return ArrayCollection
     */
    public function getPlayerSpells()
    {
        return $this->playerSpells;
    }

    /**
     * Player has spell
     *
     * @return bool
     */
    public function hasSpell(Spell $spell)
    {
        foreach ($this->playerSpells as $playerSpell) {
            if ($spell->getId() == $playerSpell->getSpell()->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set guild player
     *
     * @param GuildPlayer $guildPlayer
     *
     * @return Player
     */
    public function setGuildPlayer(GuildPlayer $guildPlayer = null)
    {
        $this->guildPlayer = $guildPlayer;

        return $this;
    }

    /**
     * Get player guild
     *
     * @return GuildPlayer
     */
    public function getGuildPlayer()
    {
        return $this->guildPlayer;
    }

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_PLAYER) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username canonical
     *
     * @return string
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * Set username canonical
     *
     * @param string $usernameCanonical
     *
     * @return Player
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * Get
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Player
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Player
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email canonical
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Set email canonical
     *
     * @param string $emailCanonical
     *
     * @return Player
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Player
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the last login time
     *
     * @return DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Set last login
     *
     * @param DateTime $date
     *
     * @return Player
     */
    public function setLastLogin(DateTime $date = null)
    {
        $this->lastLogin = $date;

        return $this;
    }

    /**
     * Get confirmation token
     *
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * Set confirmation token
     *
     * @param string $confirmationToken
     *
     * @return Player
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;
        // we need to make sure to have at least one role
        $roles[] = static::ROLE_PLAYER;

        return array_unique($roles);
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getEnabled();
    }

    /**
     * Removes a role to the user
     *
     * @param string $role
     *
     * @return Player
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Set enabled
     *
     * @param bool $enabled
     *
     * @return Player
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool) $enabled;

        return $this;
    }

    /**
     * Set password requested at
     *
     * @param DateTime $date
     *
     * @return Player
     */
    public function setPasswordRequestedAt(DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset
     *
     * @return DateTime|null
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return Player
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getUsername();
    }

    /**
     * Get Inventory Weight
     *
     * @return int
     */
    public function getInventoryMaxWeight()
    {
        return ceil(($this->getStrength() * 0.80) + ($this->getResistance() * 0.55));
    }

    /**
     * Get Inventory Weight
     *
     * @return int
     */
    public function getInventoryWeight()
    {
        return isset($this->specifications['weight']) ? $this->specifications['weight'] : 0;
    }

    /**
     * Get map vision
     *
     * @return int
     */
    public function getMapVision()
    {
        $playerVision = $this->getTotalVision();
        if (empty($playerVision) || !is_numeric($playerVision)) {
            return 1;
        }

        if ($playerVision == 1) {
            return 1;
        } elseif ($playerVision >= 2 && $playerVision <= 5) {
            return 2;
        } elseif ($playerVision >= 6 && $playerVision <= 10) {
            return 3;
        } elseif ($playerVision >= 11 && $playerVision <= 16) {
            return 4;
        } elseif ($playerVision >= 17 && $playerVision <= 23) {
            return 5;
        } elseif ($playerVision >= 24 && $playerVision <= 31) {
            return 6;
        } elseif ($playerVision >= 32 && $playerVision <= 40) {
            return 7;
        } elseif ($playerVision >= 41 && $playerVision <= 50) {
            return 8;
        } elseif ($playerVision >= 51 && $playerVision <= 64) {
            return 9;
        } elseif ($playerVision >= 65 && $playerVision <= 70) {
            return 10;
        } elseif ($playerVision >= 71 && $playerVision <= 79) {
            return 11;
        } elseif ($playerVision >= 80 && $playerVision <= 89) {
            return 12;
        } elseif ($playerVision >= 90 && $playerVision <= 99) {
            return 13;
        } elseif ($playerVision >= 100 && $playerVision <= 119) {
            return 14;
        } elseif ($playerVision >= 120) {
            return 15;
        }

        return 1;
    }

    /**
     * Add ki, action, movement, fatigue, health points
     *
     * @param string $what What points do you want to add
     * @param int $howMuch How much you want to add
     */
    public function addPoints($what, $howMuch)
    {
        switch ($what) {
            case self::KI_POINT:
                $newKi = $this->getKi() + $howMuch;
                $this->setKi($newKi > $this->getTotalMaxKi() ? $this->getTotalMaxKi() : $newKi);
                break;
            case self::ACTION_POINT:
                $newAP = $this->getActionPoints() + $howMuch;
                $this->setActionPoints($newAP > self::MAX_ACTION_POINTS ? self::MAX_ACTION_POINTS : $newAP);
                break;
            case self::MOVEMENT_POINT:
                $newMP = $this->getMovementPoints() + $howMuch;
                $this->setMovementPoints($newMP > self::MAX_MOVEMENT_POINTS ? self::MAX_MOVEMENT_POINTS : $newMP);
                break;
            case self::FATIGUE_POINT:
                $newFP = $this->getFatiguePoints() + $howMuch;
                $this->setFatiguePoints($newFP > self::MAX_FATIGUE_POINTS ? self::MAX_FATIGUE_POINTS : $newFP);
                break;
            case self::HEALTH_POINT:
                $newHealth = $this->getHealth() + $howMuch;
                $this->setHealth($newHealth > $this->getTotalMaxHealth() ? $this->getTotalMaxHealth() : $newHealth);
                break;
        }
    }

    /**
     * Use action, skill, movement and fatigue points
     *
     * @param string $what What points do you want to use
     * @param int $howMuch How much you want to use
     */
    public function usePoints($what, $howMuch)
    {
        switch ($what) {
            case self::ACTION_POINT:
                $newAP = $this->getActionPoints() - $howMuch;
                $this->setActionPoints($newAP < 0 ? 0 : $newAP);
                break;
            case self::KI_POINT:
                $newKI = $this->getKi() - $howMuch;
                $this->setKi($newKI < 0 ? 0 : $newKI);
                break;
            case self::SKILL_POINT:
                $newSK = $this->getSkillPoints() - $howMuch;
                $this->setSkillPoints($newSK < 0 ? 0 : $newSK);
                break;
            case self::MOVEMENT_POINT:
                $newMP = $this->getMovementPoints() - $howMuch;
                $this->setMovementPoints($newMP < 0 ? 0 : $newMP);
                break;
            case self::FATIGUE_POINT:
                $newFP = $this->getFatiguePoints() - $howMuch;
                $this->setFatiguePoints($newFP < 0 ? 0 : $newFP);
                break;
        }
    }

    /**
     * Get remaining time for action, ki, movement or fatigue points
     *
     * @param string $what What points do you want the time
     */
    public function getTimeRemaining($what)
    {
        switch ($what) {
            case self::ACTION_POINT:
                $method = 'Action';
                $pointTime = self::TIME_ACTION_POINT;
                break;
            case self::MOVEMENT_POINT:
                $method = 'Movement';
                $pointTime = self::TIME_MOVEMENT_POINT;
                break;
            case self::FATIGUE_POINT:
                $method = 'Fatigue';
                $pointTime = self::TIME_FATIGUE_POINT;
                break;
            case self::KI_POINT:
                $method = 'Ki';
                $pointTime = self::TIME_KI_POINT;
                break;
            default:
                throw new Exception($what . ' not found');
        }

        $originalTime = $this->{'get' . $method . 'UpdatedAt'}();
        $currentDate = new DateTime();

        return $pointTime - (int) $currentDate->diff($originalTime)->format('%i');
    }

    /**
     * Take damage
     *
     * @param int $damages Damage taken
     * @param bool $loseAP Lose action points, default true
     * @param bool $isSLap Is a nbSlap?
     *
     * @return booelean, true if player si dead, false if not
     */
    public function takeDamage($damages, $loseAP = true, $isSlap = false)
    {
        if (!is_numeric($damages) || $damages <= 0) {
            return false;
        }

        $this->setHealth($this->getHealth() - $damages);

        if ($isSlap && $this->getBetrayals() > 0) {
            $this->setBetrayals($this->getBetrayals() - 1);
            $this->setNbSlapTaken($this->getNbSlapTaken() + 1);
        }

        if ($this->getHealth() > 0) {
            return false;
        }

        $this->setHealth($this->getMaxHealth());
        $this->setKi($this->getMaxKi());
        $this->setFatiguePoints(0);
        $this->setDeathCount($this->getDeathCount() + 1);

        if ($loseAP) {
            $this->setActionPoints(ceil($this->getActionPoints() * 0.3));
        }

        return true;
    }

    /**
     * Get battle points remaining
     *
     * @param int $level Level
     *
     * @return int
     */
    public function getBattlePointsRemaining($level = null)
    {
        $level = ($level === null ? $this->getLevel() : $level);
        $battlePoints = 0;
        switch ($level) {
            // Level
            case 100:
                $battlePoints += 55660;
            // Level
            // no break
            case 99:
                $battlePoints += 54610;
            // Level
            // no break
            case 98:
                $battlePoints += 53570;
            // Level
            // no break
            case 97:
                $battlePoints += 52540;
            // Level
            // no break
            case 96:
                $battlePoints += 51520;
            // Level
            // no break
            case 95:
                $battlePoints += 50510;
            // Level
            // no break
            case 94:
                $battlePoints += 49510;
            // Level
            // no break
            case 93:
                $battlePoints += 48520;
            // Level
            // no break
            case 92:
                $battlePoints += 47530;
            // Level
            // no break
            case 91:
                $battlePoints += 46550;
            // Level
            // no break
            case 90:
                $battlePoints += 45580;
            // Level
            // no break
            case 89:
                $battlePoints += 44620;
            // Level
            // no break
            case 88:
                $battlePoints += 43670;
            // Level
            // no break
            case 87:
                $battlePoints += 42730;
            // Level
            // no break
            case 86:
                $battlePoints += 41800;
            // Level
            // no break
            case 85:
                $battlePoints += 40880;
            // Level
            // no break
            case 84:
                $battlePoints += 39970;
            // Level
            // no break
            case 83:
                $battlePoints += 39070;
            // Level
            // no break
            case 82:
                $battlePoints += 38180;
            // Level
            // no break
            case 81:
                $battlePoints += 37310;
            // Level
            // no break
            case 80:
                $battlePoints += 36450;
            // Level
            // no break
            case 79:
                $battlePoints += 35600;
            // Level
            // no break
            case 78:
                $battlePoints += 34760;
            // Level
            // no break
            case 77:
                $battlePoints += 33930;
            // Level
            // no break
            case 76:
                $battlePoints += 33110;
            // Level
            // no break
            case 75:
                $battlePoints += 32300;
            // Level
            // no break
            case 74:
                $battlePoints += 31500;
            // Level
            // no break
            case 73:
                $battlePoints += 30710;
            // Level
            // no break
            case 72:
                $battlePoints += 29930;
            // Level
            // no break
            case 71:
                $battlePoints += 29160;
            // Level
            // no break
            case 70:
                $battlePoints += 28400;
            // Level
            // no break
            case 69:
                $battlePoints += 27650;
            // Level
            // no break
            case 68:
                $battlePoints += 26910;
            // Level
            // no break
            case 67:
                $battlePoints += 26180;
            // Level
            // no break
            case 66:
                $battlePoints += 25460;
            // Level
            // no break
            case 65:
                $battlePoints += 24750;
            // Level
            // no break
            case 64:
                $battlePoints += 24050;
            // Level
            // no break
            case 63:
                $battlePoints += 23360;
            // Level
            // no break
            case 62:
                $battlePoints += 22680;
            // Level
            // no break
            case 61:
                $battlePoints += 22010;
            // Level
            // no break
            case 60:
                $battlePoints += 21350;
            // Level
            // no break
            case 59:
                $battlePoints += 20700;
            // Level
            // no break
            case 58:
                $battlePoints += 20060;
            // Level
            // no break
            case 57:
                $battlePoints += 19430;
            // Level
            // no break
            case 56:
                $battlePoints += 18810;
            // Level
            // no break
            case 55:
                $battlePoints += 18200;
            // Level
            // no break
            case 54:
                $battlePoints += 17600;
            // Level
            // no break
            case 53:
                $battlePoints += 17010;
            // Level
            // no break
            case 52:
                $battlePoints += 16430;
            // Level
            // no break
            case 51:
                $battlePoints += 15860;
            // Level
            // no break
            case 50:
                $battlePoints += 15300;
            // Level
            // no break
            case 49:
                $battlePoints += 14750;
            // Level
            // no break
            case 48:
                $battlePoints += 14210;
            // Level
            // no break
            case 47:
                $battlePoints += 13680;
            // Level
            // no break
            case 46:
                $battlePoints += 13160;
            // Level
            // no break
            case 45:
                $battlePoints += 12650;
            // Level
            // no break
            case 44:
                $battlePoints += 12150;
            // Level
            // no break
            case 43:
                $battlePoints += 11660;
            // Level
            // no break
            case 42:
                $battlePoints += 11180;
            // Level
            // no break
            case 41:
                $battlePoints += 10710;
            // Level
            // no break
            case 40:
                $battlePoints += 10250;
            // Level
            // no break
            case 39:
                $battlePoints += 9800;
            // Level
            // no break
            case 38:
                $battlePoints += 9360;
            // Level
            // no break
            case 37:
                $battlePoints += 8930;
            // Level
            // no break
            case 36:
                $battlePoints += 8510;
            // Level
            // no break
            case 35:
                $battlePoints += 8100;
            // Level
            // no break
            case 34:
                $battlePoints += 7700;
            // Level
            // no break
            case 33:
                $battlePoints += 7310;
            // Level
            // no break
            case 32:
                $battlePoints += 6930;
            // Level
            // no break
            case 31:
                $battlePoints += 6560;
            // Level
            // no break
            case 30:
                $battlePoints += 6200;
            // Level
            // no break
            case 29:
                $battlePoints += 5850;
            // Level
            // no break
            case 28:
                $battlePoints += 5510;
            // Level
            // no break
            case 27:
                $battlePoints += 5180;
            // Level
            // no break
            case 26:
                $battlePoints += 4860;
            // Level
            // no break
            case 25:
                $battlePoints += 4550;
            // Level
            // no break
            case 24:
                $battlePoints += 4250;
            // Level
            // no break
            case 23:
                $battlePoints += 3960;
            // Level
            // no break
            case 22:
                $battlePoints += 3680;
            // Level
            // no break
            case 21:
                $battlePoints += 3410;
            // Level
            // no break
            case 20:
                $battlePoints += 3150;
            // Level
            // no break
            case 19:
                $battlePoints += 2900;
            // Level
            // no break
            case 18:
                $battlePoints += 2660;
            // Level
            // no break
            case 17:
                $battlePoints += 2430;
            // Level
            // no break
            case 16:
                $battlePoints += 2210;
            // Level
            // no break
            case 15:
                $battlePoints += 2000;
            // Level
            // no break
            case 14:
                $battlePoints += 1800;
            // Level
            // no break
            case 13:
                $battlePoints += 1610;
            // Level
            // no break
            case 12:
                $battlePoints += 1430;
            // Level
            // no break
            case 11:
                $battlePoints += 1260;
            // Level
            // no break
            case 10:
                $battlePoints += 1100;
            // Level
            // no break
            case 9:
                $battlePoints += 950;
            // Level
            // no break
            case 8:
                $battlePoints += 810;
            // Level
            // no break
            case 7:
                $battlePoints += 680;
            // Level
            // no break
            case 6:
                $battlePoints += 560;
            // Level
            // no break
            case 5:
                $battlePoints += 450;
            // Level
            // no break
            case 4:
                $battlePoints += 350;
            // Level
            // no break
            case 3:
                $battlePoints += 260;
            // Level
            // no break
            case 2:
                $battlePoints += 180;
            // Level
            // no break
            case 1:
                $battlePoints += 110;
            // Level
            // no break
            case 0:
                $battlePoints += 50;
        }

        return $battlePoints;
    }

    /**
     * Find player best skill
     *
     * @param string $exclude Skill to exclude
     *
     * @return string
     */
    protected function findBestSkill($exclude = null)
    {
        $methods = [
            'st' => 'Strength',
            'ac' => 'Accuracy',
            'ag' => 'Agility',
            'an' => 'Analysis',
            'sk' => 'Skill',
            'in' => 'Intellect',
            're' => 'Resistance',
            'vi' => 'Vision',
            'mk' => 'MaxKi',
            'mh' => 'MaxHealth',
        ];

        $skillValue = 0;
        $skillName = null;

        foreach ($methods as $name => $method) {
            if ($name == $exclude) {
                continue;
            }

            $skillLevel = call_user_func([$this, 'get' . $method]);
            if ($method == 'MaxKi') {
                $skillLevel /= 2;
            } elseif ($method == 'MaxHealth') {
                $skillLevel -= (500 - (($this->getLevel() - 1) * 10));
            }

            if ($skillLevel > $skillValue) {
                $skillValue = $skillLevel;
                $skillName = $name;
            }
        }

        return $skillName;
    }

    /**
     * Get character class name
     *
     * @return string
     */
    public function getClass($random = false)
    {
        if (empty($random)) {
            $firstSkill = $this->findBestSkill();
            $secondSkill = $this->findBestSkill($firstSkill);
        } else {
            $specifications = ['st', 'ac', 'ag', 'an', 'sk', 'in', 're', 'vi', 'mk', 'mh'];
            $rand = array_rand($specifications, 2);
            $firstSkill = $specifications[$rand[0]];
            $secondSkill = $specifications[$rand[1]];
        }

        // Everything with strength
        if (($firstSkill == 'st' and $secondSkill == 'ac') or ($firstSkill == 'ac' and $secondSkill == 'st')) {
            $class = 'warrior';
        } elseif (($firstSkill == 'st' and $secondSkill == 'ag') or ($firstSkill == 'ag' and $secondSkill == 'st')) {
            $class = 'champion';
        } elseif (($firstSkill == 'st' and $secondSkill == 'vi') or ($firstSkill == 'vi' and $secondSkill == 'st')) {
            $class = 'lordPlains';
        } elseif (($firstSkill == 'st' and $secondSkill == 'in') or ($firstSkill == 'in' and $secondSkill == 'st')) {
            $class = 'berserker';
        } elseif (($firstSkill == 'st' and $secondSkill == 'an') or ($firstSkill == 'an' and $secondSkill == 'st')) {
            $class = 'templar';
        } elseif (($firstSkill == 'st' and $secondSkill == 'sk') or ($firstSkill == 'sk' and $secondSkill == 'st')) {
            $class = 'paladin';
        } elseif (($firstSkill == 'st' and $secondSkill == 're') or ($firstSkill == 're' and $secondSkill == 'st')) {
            $class = 'gladiator';
        } elseif (($firstSkill == 'st' and $secondSkill == 'mh') or ($firstSkill == 'mh' and $secondSkill == 'st')) {
            $class = 'imperialGuard';

        // intellect
        } elseif (($firstSkill == 'in' and $secondSkill == 'mk') or ($firstSkill == 'mk' and $secondSkill == 'in')) {
            $class = 'magus';
        } elseif (($firstSkill == 'in' and $secondSkill == 'vi') or ($firstSkill == 'vi' and $secondSkill == 'in')) {
            $class = 'medium';
        } elseif (($firstSkill == 'in' and $secondSkill == 'ag') or ($firstSkill == 'in' and $secondSkill == 'ag')) {
            $class = 'illusionist';
        } elseif (($firstSkill == 'in' and $secondSkill == 'mh') or ($firstSkill == 'mh' and $secondSkill == 'in')) {
            $class = 'imperialMagus';
        } elseif (($firstSkill == 'in' and $secondSkill == 'an') or ($firstSkill == 'an' and $secondSkill == 'in')) {
            $class = 'sage';
        } elseif (($firstSkill == 'in' and $secondSkill == 're') or ($firstSkill == 're' and $secondSkill == 'in')) {
            $class = 'warMagus';
        } elseif (($firstSkill == 'in' and $secondSkill == 'sk') or ($firstSkill == 'sk' and $secondSkill == 'in')) {
            $class = 'healer';
        } elseif (($firstSkill == 'in' and $secondSkill == 'ac') or ($firstSkill == 'ac' and $secondSkill == 'in')) {
            $class = 'blackMagus';

        // Agility
        } elseif (($firstSkill == 'ag' and $secondSkill == 'ac') or ($firstSkill == 'ac' and $secondSkill == 'ag')) {
            $class = 'thief';
        } elseif (($firstSkill == 'ag' and $secondSkill == 'mh') or ($firstSkill == 'mh' and $secondSkill == 'ag')) {
            $class = 'imperialNinja';
        } elseif (($firstSkill == 'ag' and $secondSkill == 'sk') or ($firstSkill == 'sk' and $secondSkill == 'ag')) {
            $class = 'medicalNinja';
        } elseif (($firstSkill == 'ag' and $secondSkill == 'vi') or ($firstSkill == 'vi' and $secondSkill == 'ag')) {
            $class = 'scout';
        } elseif (($firstSkill == 'ag' and $secondSkill == 'an') or ($firstSkill == 'an' and $secondSkill == 'ag')) {
            $class = 'specialist';

        // Skill
        } elseif (($firstSkill == 'sk' and $secondSkill == 're') or ($firstSkill == 're' and $secondSkill == 'sk')) {
            $class = 'soldierStone';
        } elseif (($firstSkill == 'sk' and $secondSkill == 'mh') or ($firstSkill == 'mh' and $secondSkill == 'sk')) {
            $class = 'imperialHealer';
        } elseif (($firstSkill == 'sk' and $secondSkill == 'an') or ($firstSkill == 'an' and $secondSkill == 'sk')) {
            $class = 'priest';
        } elseif (($firstSkill == 'vi' and $secondSkill == 'sk') or ($firstSkill == 'sk' and $secondSkill == 'vi')) {
            $class = 'plainsHealer';
        } elseif (($firstSkill == 'ac' and $secondSkill == 'sk') or ($firstSkill == 'sk' and $secondSkill == 'ac')) {
            $class = 'highHealer';
        } elseif (($firstSkill == 'mk' and $secondSkill == 'sk') or ($firstSkill == 'mk' and $secondSkill == 'sk')) {
            $class = 'bishop';

        // Analysis
        } elseif (($firstSkill == 'an' and $secondSkill == 'vi') or ($firstSkill == 'vi' and $secondSkill == 'an')) {
            $class = 'scout';
        } elseif (($firstSkill == 'an' and $secondSkill == 'mh') or ($firstSkill == 'mh' and $secondSkill == 'an')) {
            $class = 'highResearcher';

        // Ki
        } elseif (($firstSkill == 'mk' and $secondSkill == 'in') or ($firstSkill == 'in' and $secondSkill == 'mk')) {
            $class = 'magus';
        } else {
            $class = 'none';
        }

        return 'player.class.' . $class;
    }

    /**
     * Is Player entity is a real player
     *
     * @return bool
     */
    public function isPlayer()
    {
        return in_array($this->getSide()->getId(), [Side::GOOD, Side::BAD]);
    }

    /**
     * @ORM\PostLoad
     * @ORM\PreFlush
     *
     * Reload objects and total bonus
     */
    public function reloadBonus()
    {
        $this->specifications = [
            'objects' => [
                'strength' => 0,
                'accuracy' => 0,
                'agility' => 0,
                'analysis' => 0,
                'skill' => 0,
                'intellect' => 0,
                'resistance' => 0,
                'vision' => 0,
                'max_ki' => 0,
                'max_health' => 0,
            ],
            'total' => [
                'strength' => $this->getStrength(),
                'accuracy' => $this->getAccuracy(),
                'agility' => $this->getAgility(),
                'analysis' => $this->getAnalysis(),
                'skill' => $this->getSkill(),
                'intellect' => $this->getIntellect(),
                'resistance' => $this->getResistance(),
                'vision' => $this->getVision(),
                'max_ki' => $this->getMaxKi(),
                'max_health' => $this->getMaxHealth(),
            ],
            'weight' => 0,
        ];

        foreach ($this->getPlayerObjects() as $playerObject) {
            if (empty($playerObject->getNumber()) || !$playerObject->getObject()->isEnabled()) {
                continue;
            }

            $this->specifications['weight'] += $playerObject->getObject()->getWeight();
            if (empty($playerObject->isEquipped())) {
                continue;
            }

            foreach ($playerObject->getObject()->getBonus() as $type => $bonus) {
                if (!isset($this->specifications['total'][$type])) {
                    continue;
                }

                $this->specifications['objects'][$type] += $bonus;
                $this->specifications['total'][$type] += $bonus;
            }
        }
    }

    /**
     * @ORM\PreUpdate
     * @ORM\PrePersist
     *
     * Reload objects and total bonus
     */
    public function updateCanonicals()
    {
        $this->setUsernameCanonical(strtolower($this->getUsername()));
        $this->setEmailCanonical(strtolower($this->getEmail()));
    }

    /**
     * Check if user can convert AP to MP
     *
     * @return bool
     */
    public function canConvert()
    {
        return $this->getActionPoints() >= 20 && $this->getMovementPoints() + 40 <= Player::MAX_MOVEMENT_POINTS;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Whether the user is connected or not
     *
     * @return bool
     */
    public function isConnected()
    {
        $delay = new DateTime('2 minutes ago');

        return $this->getLastLogin() > $delay;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize([
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
        ) = $data;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("can_be_healed")
     * @JMS\Expose
     */
    public function canBeHealed()
    {
        return $this->getHealth() < $this->getTotalMaxHealth();
    }

    /**
     * Get image path
     *
     * @return string
     */
    public function getImagePath()
    {
        $directory = $this->isPlayer() ? 'players' : 'npc';

        return sprintf('/bundles/dbaadmin/images/avatars/%s/%s', $directory, $this->getImage());
    }
}
