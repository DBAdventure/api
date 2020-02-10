<?php

namespace Dba\GameBundle\Repository;

use Dba\GameBundle\Entity\Inbox;
use Dba\GameBundle\Entity\Player;
use Doctrine\ORM\EntityRepository;

class InboxRepository extends EntityRepository
{
    /**
     * Find archive
     *
     * @param Player $player Player
     *
     * @return array
     */
    public function findArchive(Player $player)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->where('i.sender = :player AND i.senderDirectory = :directory')
            ->orWhere('i.recipient = :player AND i.recipientDirectory = :directory');
        $qb->orderBy('i.createdAt', 'DESC');
        $qb->setParameter('player', $player);
        $qb->setParameter('directory', Inbox::DIRECTORY_ARCHIVE);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get number of unread messages
     *
     * @param Player $player Player
     *
     * @return int
     */
    public function countUnreadMessages(Player $player)
    {
        $qb = $this->createQueryBuilder('i');

        return $qb->select('COUNT(i)')
            ->andWhere('i.recipient = :player')
            ->andWhere('i.recipientDirectory = :directory')
            ->andWhere('i.status = :status')
            ->setParameters([
                'player' => $player,
                'directory' => Inbox::DIRECTORY_INBOX,
                'status' => Inbox::STATUS_UNREAD,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }
}
