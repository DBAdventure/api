<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

/**
 * Quest Npc
 *
 * @ORM\Table(name="quest_npc", indexes={@ORM\Index(name="quest_npc_quest_id", columns={"quest_id"}),
   @ORM\Index(name="quest_npc_race_id", columns={"race_id"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\QuestNpcRepository")
 */
class QuestNpc
{
    /**
     * @var Quest
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Quest", fetch="EAGER", inversedBy="npcsNeeded")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quest_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     * @JMS\Exclude
     */
    private $quest;

    /**
     * @var Race
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Race", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="race_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $race;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $number = 1;

    /**
     * Set number
     *
     * @param string $number
     *
     * @return QuestRace
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return integer
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set quest
     *
     * @param Quest $quest
     *
     * @return QuestRace
     */
    public function setQuest(Quest $quest)
    {
        $this->quest = $quest;

        return $this;
    }

    /**
     * Get quest
     *
     * @return Quest
     */
    public function getQuest()
    {
        return $this->quest;
    }

    /**
     * Set Race
     *
     * @param Race $race
     *
     * @return Quest
     */
    public function setRace($race)
    {
        $this->race = $race;

        return $this;
    }

    /**
     * Get object
     *
     * @return Race
     */
    public function getRace()
    {
        return $this->race;
    }
}
