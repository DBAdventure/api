<?php

namespace Dba\GameBundle\Repository;

use DateTime;
use Dba\GameBundle\Entity\Side;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Dba\GameBundle\Entity\Player;

class PlayerRepository extends EntityRepository
{
    /**
     * Count by enabled
     *
     * @return integer
     */
    public function countByEnabled()
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->select('COUNT(p)')
            ->andWhere('p.enabled = TRUE')
            ->andWhere('p.side IN (:sides)')
            ->setParameters([
                'sides' => [
                    Side::BAD,
                    Side::GOOD,
                ]
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count by race
     *
     * @param integer $race Race
     *
     * @return integer
     */
    public function countByRace($race)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->select('COUNT(p)')
            ->where('p.race = :race')
            ->andWhere('p.enabled = TRUE')
            ->setParameters(['race' => $race])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count by side
     *
     * @param integer $side Side
     *
     * @return integer
     */
    public function countBySide($side)
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->select('COUNT(p)')
            ->where('p.side = :side')
            ->andWhere('p.enabled = TRUE')
            ->setParameters(['side' => $side])
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get online players
     *
     * @return array
     */
    public function getOnlinePlayers()
    {
        $qb = $this->createQueryBuilder('p');
        return $qb->select('p')
            ->where('p.lastLogin > :delay')
            ->andWhere('p.enabled = TRUE')
            ->setParameters(['delay' => new DateTime('-2 minutes')])
            ->getQuery()
            ->getResult();
    }

    /**
     * Find nearest player
     *
     * @param Player $player Should be a npc
     */
    public function findNearestPlayers(Player $player)
    {
        $request = <<<EOT
SELECT
  target.id
FROM player
LEFT JOIN player AS target ON player.id != target.id
WHERE player.id = :id
  AND target.map_id = player.map_id
  AND target.x BETWEEN (player.x - :vision) AND (player.x + :vision)
  AND target.y BETWEEN (player.y - :vision) AND (player.y + :vision)
  AND (
    player.target_id = player.id OR
    target.side_id IN (:sides)
  )
  AND target.enabled = true
EOT;
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Player::class, 'target');
        $rsm->addFieldResult('target', 'id', 'id');

        $query = $this->getEntityManager()->createNativeQuery($request, $rsm);
        $query->setParameters([
            'id' => $player->getId(),
            'sides' => [Side::BAD, Side::GOOD],
            'vision' => 5 // @TODO Npc vision, maybe must change
        ]);

        $players = [];
        foreach ($query->getResult() as $player) {
            $this->getEntityManager()->refresh($player);
            $players[] = $player;
        }

        return $players;
    }
}
