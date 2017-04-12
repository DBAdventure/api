<?php

namespace Dba\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MapImageFile
 *
 * @ORM\Table(name="map_image_file", indexes={@ORM\Index(name="map_image_file_id", columns={"map_image_id"})})
 * @ORM\Entity
 */
class MapImageFile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="damage", type="integer", nullable=false)
     */
    private $damage = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="period", type="integer", nullable=false)
     */
    private $period = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=120, nullable=false)
     */
    private $file;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var MapImage
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\MapImage", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="map_image_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $mapImage;



    /**
     * Set damage
     *
     * @param integer $damage
     *
     * @return MapImageFile
     */
    public function setDamage($damage)
    {
        $this->damage = $damage;

        return $this;
    }

    /**
     * Get damage
     *
     * @return integer
     */
    public function getDamage()
    {
        return $this->damage;
    }

    /**
     * Set period
     *
     * @param integer $period
     *
     * @return MapImageFile
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return integer
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return MapImageFile
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
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
     * Set mapImage
     *
     * @param MapImage $mapImage
     *
     * @return MapImageFile
     */
    public function setMapImage(MapImage $mapImage = null)
    {
        $this->mapImage = $mapImage;

        return $this;
    }

    /**
     * Get mapImage
     *
     * @return MapImage
     */
    public function getMapImage()
    {
        return $this->mapImage;
    }
}
