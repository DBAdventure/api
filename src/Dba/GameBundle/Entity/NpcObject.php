<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Npc Object
 *
 * @ORM\Table(name="npc_object")
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\NpcObjectRepository")
 */
class NpcObject
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=80, nullable=false)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="list", type="json_array", nullable=false)
     */
    private $list;

    /**
     * @var integer
     *
     * @ORM\Column(name="luck", type="integer", nullable=false)
     * @Assert\NotBlank()
     */
    private $luck = 100;

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
     * Set luck
     *
     * @param string $luck
     *
     * @return QuestObject
     */
    public function setLuck($luck)
    {
        $this->luck = $luck;

        return $this;
    }

    /**
     * Get luck
     *
     * @return integer
     */
    public function getLuck()
    {
        return $this->luck;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Building
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

    /**
     * Set list
     *
     * @param array $list
     *
     * @return NpcObject
     */
    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }
}
