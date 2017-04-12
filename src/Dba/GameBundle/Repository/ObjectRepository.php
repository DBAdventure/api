<?php

namespace Dba\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ObjectRepository extends EntityRepository
{
    /**
     * Count objects
     *
     * @return integer
     */
    public function countObjects()
    {
        $qb = $this->createQueryBuilder('o');
        return $qb->select('COUNT(o)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
