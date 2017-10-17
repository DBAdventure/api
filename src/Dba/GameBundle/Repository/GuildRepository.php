<?php

namespace Dba\GameBundle\Repository;

use Dba\GameBundle\Entity\GuildRank;
use Dba\GameBundle\Entity\Player;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class GuildRepository extends EntityRepository
{
    /**
     * Find all admins
     *
     * @return array
     */
    public function findOtherAdmins($player)
    {
        $request = <<<EOT
SELECT
  player.id
FROM player
INNER JOIN guild_player AS gp ON gp.player_id = player.id
INNER JOIN guild_rank AS gr ON gr.id = gp.rank_id
WHERE gp.guild_id = :guild
  AND player.id != :player
  AND gr.role = :role
  AND gp.enabled = true
EOT;
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult(Player::class, 'player');
        $rsm->addFieldResult('player', 'id', 'id');

        $query = $this->getEntityManager()->createNativeQuery($request, $rsm);
        $query->setParameters([
            'player' => $player->getId(),
            'role' => GuildRank::ROLE_ADMIN,
            'guild' => $player->getGuildPlayer()->getGuild()->getId(),
        ]);

        $players = [];
        foreach ($query->getResult() as $player) {
            $this->getEntityManager()->refresh($player);
            $players[] = $player;
        }

        return $players;
    }

    /**
     * Count active guilds
     *
     * @return integer
     */
    public function countGuilds()
    {
        $qb = $this->createQueryBuilder('g');
        return $qb->select('COUNT(g)')
            ->where('g.enabled = true')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
