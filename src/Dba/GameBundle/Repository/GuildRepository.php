<?php

namespace Dba\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GuildRepository extends EntityRepository
{
    /**
     * Count active guilds
     *
     * @return integer
     */
    public function countGuilds()
    {
        $qb = $this->createQueryBuilder('o');
        return $qb->select('COUNT(o)')
            ->where('o.enabled = true')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
