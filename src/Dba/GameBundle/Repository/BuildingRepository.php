<?php

namespace Dba\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BuildingRepository extends EntityRepository
{
    /**
     * Count buildings
     *
     * @return integer
     */
    public function countBuildings()
    {
        $qb = $this->createQueryBuilder('b');
        return $qb->select('COUNT(b)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
