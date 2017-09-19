<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\Annotations;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dba\GameBundle\Entity\Side;

class MiscellaneousController extends BaseController
{
    /**
     * @Annotations\Post("/contact")
     */
    public function contactAction()
    {
        return $this->badRequest('Not Implemented yet');
    }

    /**
     * @Annotations\Get("/ranking/{what}")
     */
    public function rankingAction(Request $request, $what)
    {
        $rankingList = [
            'battle-points' => [
                'label' => 'ranking.battlePoints',
                'field' => 'p.battlePoints'
            ],
            'masterkill' => [
                'label' => 'ranking.serialKillers',
                'field' => 'masterKill',
                'description' => 'ranking.description.serialKillers'
            ],
            'good-killer' => [
                'label' => 'ranking.assassins',
                'field' => 'p.nbKillGood',
                'description' => 'ranking.description.assassins'
            ],
            'bad-killer' => [
                'label' => 'ranking.crusaders',
                'field' => 'p.nbKillBad',
                'description' => 'ranking.description.crusaders'
            ],
            'bounty-hunter' => [
                'label' => 'ranking.bounty',
                'field' => 'p.nbWanted'
            ],
            'hq-batter' => [
                'label' => 'ranking.hq.hitters',
                'field' => 'p.nbHitHq',
                'description' => 'ranking.description.hq.hitters'
            ],
            'hq-damage' => [
                'label' => 'ranking.hq.terrors',
                'field' => 'p.nbDamageHq',
                'description' => 'ranking.description.hq.terrors'
            ],
            'hq-killer' => [
                'label' => 'ranking.hq.killers',
                'field' => 'p.nbKillHq',
                'description' => 'ranking.description.hq.killers'
            ],
            'npc_killer' => [
                'label' => 'ranking.hunters',
                'field' => 'p.nbKillNpc',
                'description' => 'ranking.description.hunters'
            ],
            'death' => [
                'label' => 'ranking.victims',
                'field' => 'p.deathCount',
                'description' => 'ranking.description.victims'
            ],
            'slap-given' => [
                'label' => 'ranking.slap.given',
                'field' => 'p.nbSlapGiven'
            ],
            'slap-taken' => [
                'label' => 'ranking.slap.taken',
                'field' => 'p.nbSlapTaken'
            ]
        ];

        if (empty($rankingList[$what])) {
            $what = 'battle-points';
        }

        $playerRepo = $this->repos()->getPlayerRepository();
        $limitPerPage = 60;
        $page = $request->query->get('page', 1);
        $page = $page < 1 ? 1 : $page;

        $qb = $playerRepo->createQueryBuilder('p');
        $qb->addSelect('(p.nbKillGood + p.nbKillBad + p.nbKillNpc) AS masterKill')
            ->where(
                $qb->expr()->in(
                    'p.side',
                    [Side::GOOD, Side::BAD]
                )
            )
            ->andWhere('p.enabled = true');

        $type = $request->query->get('type');
        $player = $this->getUser();
        if (!empty($player) && !empty($type) && $type == 'guild') {
            $guildPlayer = $player->getGuildPlayer();
            if (!empty($guildPlayer) && $guildPlayer->isEnabled()) {
                $qb->innerJoin('p.guildPlayer', 'gp', 'WITH', 'gp.player = p')
                    ->andWhere('gp.guild = :guild')
                    ->setParameter('guild', $guildPlayer->getGuild());
            }
        }

        $who = $request->query->get('who');
        if (!empty($who) && ($playerSearched = $playerRepo->findOneByName($who))) {
            $orderField = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $rankingList[$what]['field']));
            if ($orderField == 'master_kill') {
                $orderField = '(p.nb_kill_good + p.nb_kill_bad + p.nb_kill_npc)';
            }
            $sqlRequest = <<<EOF
SELECT
 r.row_number
FROM (
  SELECT
    id,
    (p.nb_kill_good + p.nb_kill_bad + p.nb_kill_npc) AS master_kill,
    ROW_NUMBER() OVER(ORDER BY $orderField DESC) AS row_number
  FROM player AS p
  WHERE p.side_id IN (:sides) AND p.enabled = true
) AS r
WHERE r.id = :id
EOF;
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('row_number', 'row_number');
            $query = $this->em()->createNativeQuery($sqlRequest, $rsm);
            $query->setParameters([
                'id' =>  $playerSearched->getId(),
                'sides' => [Side::BAD, Side::GOOD]
            ]);
            $rowNumber = $query->getSingleScalarResult();
            $page = ceil($rowNumber / $limitPerPage);
        }

        $qb->setFirstResult(($page - 1) * $limitPerPage)
            ->setMaxResults($limitPerPage)
            ->addOrderBy($rankingList[$what]['field'], 'DESC');
        $ranking = new Paginator($qb, false);

        return [
            'count' => count($ranking),
            'current' => $page,
            'limit' => $limitPerPage,
            'what' => $what,
            'type' => $type,
            'who' => !empty($playerSearched) ? $playerSearched->getName() : null,
            'result' => [
                'ranking' => $ranking,
                'rankingList' => $rankingList,
            ]
        ];
    }
}
