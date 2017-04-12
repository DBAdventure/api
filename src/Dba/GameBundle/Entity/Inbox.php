<?php

namespace Dba\GameBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Inbox
 *
 * @ORM\Table(name="inbox", indexes={@ORM\Index(name="inbox_sender_id", columns={"sender_id"}),
              @ORM\Index(name="inbox_recipient_id", columns={"recipient_id"})})
 * @ORM\Entity(repositoryClass="Dba\GameBundle\Repository\InboxRepository")
 */
class Inbox
{
    const DIRECTORY_INBOX = 'inbox';
    const DIRECTORY_ARCHIVE = 'archive';
    const DIRECTORY_OUTBOX = 'outbox';
    const DIRECTORY_TRASH = 'trash';
    const DIRECTORY_GUILD = 'guild';

    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;
    const STATUS_DELETE = 2;

    /**
     * @var ArrayCollection
     */
    protected $recipients;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status = self::STATUS_UNREAD;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", nullable=false)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="sender_directory", type="string", length=20, nullable=false)
     */
    private $senderDirectory = self::DIRECTORY_OUTBOX;

    /**
     * @var integer
     *
     * @ORM\Column(name="recipient_directory", type="string", length=20, nullable=false)
     */
    private $recipientDirectory = self::DIRECTORY_INBOX;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Player", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recipient_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $recipient;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="Dba\GameBundle\Entity\Player", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sender_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private $sender;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->recipients = new ArrayCollection();
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
     * Set name
     *
     * @param string $subject
     *
     * @return Inbox
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
     * Set message
     *
     * @param string $message
     *
     * @return Inbox
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set sender
     *
     * @param Player $sender
     *
     * @return Inbox
     */
    public function setSender(Player $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return Player
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set recipient
     *
     * @param Player $recipient
     *
     * @return Inbox
     */
    public function setRecipient(Player $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return Player
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set sender directory
     *
     * @param integer $senderDirectory
     *
     * @return Inbox
     */
    public function setSenderDirectory($senderDirectory)
    {
        $this->senderDirectory = $senderDirectory;

        return $this;
    }

    /**
     * Get sender directory
     *
     * @return integer
     */
    public function getSenderDirectory()
    {
        return $this->senderDirectory;
    }

    /**
     * Set recipient directory
     *
     * @param integer $recipientDirectory
     *
     * @return Inbox
     */
    public function setRecipientDirectory($recipientDirectory)
    {
        $this->recipientDirectory = $recipientDirectory;

        return $this;
    }

    /**
     * Get recipient directory
     *
     * @return integer
     */
    public function getRecipientDirectory()
    {
        return $this->recipientDirectory;
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
     * Set createdAt
     *
     * @param DateTime $createdAt
     *
     * @return Inbox
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return Inbox
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Add recipient
     *
     * @param string $recipient
     *
     * @return Inbox
     */
    public function addRecipient(Player $recipient)
    {
        $this->recipients[$recipient->getId()] = $recipient;

        return $this;
    }

    /**
     * Remove recipient
     *
     * @param string $recipient
     *
     * @return Inbox
     */
    public function removeRecipient(Player $recipient)
    {
        if (isset($this->recipients[$recipient->getId()])) {
            unset($this->recipients[$recipient->getId()]);
        }

        return $this;
    }

    /**
     * Get Event
     *
     * @return array
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Magic method for clone
     */
    public function __clone()
    {
        $this->id = null;
    }

    /**
     * Check if user can reply
     *
     * @param Player $player Player
     *
     * @return boolean
     */
    public function canReply(Player $player)
    {
        return $this->getRecipient()->getId() == $player->getId();
    }

    /**
     * Check if user can archive
     *
     * @param Player $player Player
     *
     * @return boolean
     */
    public function canArchive()
    {
        return in_array(
            $this->getRecipientDirectory(),
            [self::DIRECTORY_INBOX]
        );
    }
}
