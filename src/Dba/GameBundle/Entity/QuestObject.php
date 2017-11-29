<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Quest Object
 *
 * @ORM\Table(name="quest_object", indexes={@ORM\Index(name="quest_object_quest_id", columns={"quest_id"}),
   @ORM\Index(name="quest_object_object_id", columns={"object_id"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\QuestObjectRepository")
 */
class QuestObject
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
     */
    private $quest;

    /**
     * @var Object
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Object", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="object_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $object;

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
     * Set object
     *
     * @param Object $object
     *
     * @return Quest
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return Object
     */
    public function getObject()
    {
        return $this->object;
    }
}
