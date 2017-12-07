<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Quest Npc Object
 *
 * @ORM\Table(name="quest_npc_object", indexes={@ORM\Index(name="quest_npc_object_quest_id", columns={"quest_id"}),
   @ORM\Index(name="quest_npc_object_npc_object_id", columns={"npc_object_id"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\QuestObjectRepository")
 */
class QuestNpcObject
{
    /**
     * @var Quest
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Quest", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="quest_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     * @JMS\Exclude
     */
    private $quest;

    /**
     * @var NpcObject
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\NpcObject", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="npc_object_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $npcObject;

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
     * @return QuestObject
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
     * @return QuestObject
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
     * Set npc object
     *
     * @param NpcObject $npcObject
     *
     * @return Quest
     */
    public function setNpcObject($npcObject)
    {
        $this->npcObject = $npcObject;

        return $this;
    }

    /**
     * Get npc object
     *
     * @return NpcObject
     */
    public function getNpcObject()
    {
        return $this->npcObject;
    }
}
