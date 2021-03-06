<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MapObjectType
 *
 * @ORM\Table(name="map_object_type", indexes={@ORM\Index(name="map_object_type_name", columns={"name"})})
 * @ORM\Entity
 */
class MapObjectType
{
    const ZENI = 1;
    const BUSH = 2;
    const BOX = 3;
    const CAPSULE_BLUE = 4;
    const CAPSULE_RED = 5;
    const CAPSULE_ORANGE = 6;
    const CAPSULE_BLACK = 7;
    const CAPSULE_GREEN = 8;
    const SIGN = 9;

    const TYPE_LIST = [
        self::ZENI => 'zeni',
        self::BUSH => 'bush',
        self::BOX => 'box',
        self::CAPSULE_BLUE => 'capsule.blue',
        self::CAPSULE_RED => 'capsule.red',
        self::CAPSULE_ORANGE => 'capsule.orange',
        self::CAPSULE_BLACK => 'capsule.black',
        self::CAPSULE_GREEN => 'capsule.green',
        self::SIGN => 'sign',
    ];

    /**
     * @var int
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
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=50, nullable=false)
     */
    private $image;

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
     * Set image
     *
     * @param string $image
     *
     * @return Building
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
}
