<?php

namespace Dba\GameBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Mail
 *
 * @ORM\Table(name="mail")
 * @ORM\Entity
 */
class Mail
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
     * @var string
     *
     * @ORM\Column(name="subject", type="string", nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="template_name", type="string", nullable=false)
     */
    private $templateName;

    /**
     * @var array
     *
     * @ORM\Column(name="parameters", type="json_array", nullable=false)
     */
    private $parameters;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Player")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="player_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $player;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @var string
     *
     * @ORM\Column(name="message_sent", type="text", nullable=true)
     */
    private $messageSent;

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
     * Set subject
     *
     * @param string $subject
     *
     * @return Mail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set player
     *
     * @param Player $player
     *
     * @return Mail
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
     * Set template name
     *
     * @param string $templateName
     *
     * @return Mail
     */
    public function setTemplateName($templateName)
    {
        $this->templateName = $templateName;

        return $this;
    }

    /**
     * Get template name
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     *
     * @return Mail
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set createdAt
     *
     * @param DateTime $createdAt
     *
     * @return Mail
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
     * Set sentAt
     *
     * @param DateTime $sentAt
     *
     * @return Mail
     */
    public function setSentAt(DateTime $sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Set message sent
     *
     * @param string $messageSent
     *
     * @return Mail
     */
    public function setMessageSent($messageSent)
    {
        $this->messageSent = $messageSent;

        return $this;
    }

    /**
     * Get message sent
     *
     * @return string
     */
    public function getMessageSent()
    {
        return $this->messageSent;
    }
}
