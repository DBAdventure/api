<?php

namespace Dba\GameBundle\Repository;

use Dba\GameBundle\Entity\Map;
use Dba\GameBundle\Entity\MapBonus;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Services\TemplateService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMapping;

class MapRepository extends EntityRepository
{
    protected $defaultMap;
    protected $items = [];

    /**
     * Get player case data
     *
     * @param Player $player Player
     * @param string $period Period of the day
     *
     * @return array
     */
    public function getCaseData(Player $player, $period)
    {
        $request = <<<EOT
SELECT
  mif.file AS file,
  mbs.type AS bonus
FROM player AS p
INNER JOIN map_box AS mb ON (p.map_id = mb.map_id)
INNER JOIN map_bonus AS mbs ON (mb.map_bonus_id = mbs.id)
INNER JOIN map_image_file AS mif ON
  (mb.map_image_id = mif.map_image_id AND ROUND(mb.damage/50) = mif.damage AND mif.period = :period)
INNER JOIN map AS m ON (m.id = p.map_id)
WHERE p.id = :id
  AND mb.x = p.x
  AND mb.y = p.y
EOT;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('file', 'file');
        $rsm->addScalarResult('bonus', 'bonus');

        $query = $this->getEntityManager()->createNativeQuery($request, $rsm);
        $query->setParameters([
            'period' => (int) ($period == TemplateService::PERIOD_NIGHT),
            'id' => $player->getId(),
        ]);

        return $query->getSingleResult();
    }

    /**
     * Generate map boxes without items
     *
     * @param Player $player Player
     * @param string $period Period of the day
     *
     * @return array
     */
    public function generate(Player $player, $period)
    {
        $request = <<<EOT
SELECT
  mb.x as x_map,
  mb.y as y_map,
  mif.file AS box_file,
  mbs.type AS bonus
FROM player AS p
INNER JOIN map_box AS mb ON (p.map_id = mb.map_id)
INNER JOIN map_bonus AS mbs ON (mb.map_bonus_id = mbs.id)
INNER JOIN map_image_file AS mif ON
  (mb.map_image_id = mif.map_image_id AND ROUND(mb.damage/50) = mif.damage AND mif.period = :period)
INNER JOIN map AS m ON (m.id = p.map_id)
WHERE p.id = :id
  AND mb.x BETWEEN (p.x - :vision) AND (p.x + :vision)
  AND mb.y BETWEEN (p.y - :vision) AND (p.y + :vision)
  AND mb.x >= 1
  AND mb.y >= 1
  AND mb.x <= m.max_x
  AND mb.y <= m.max_y
EOT;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('x_map', 'x_map');
        $rsm->addScalarResult('y_map', 'y_map');
        $rsm->addScalarResult('box_file', 'box_file');
        $rsm->addScalarResult('bonus', 'bonus');

        $query = $this->getEntityManager()->createNativeQuery($request, $rsm);
        $query->setParameters([
            'period' => (int) ($period == TemplateService::PERIOD_NIGHT),
            'id' => $player->getId(),
            'vision' => $player->getMapVision(),
        ]);

        $mapData = [];
        foreach ($query->getResult() as $box) {
            if (empty($mapData[$box['x_map']])) {
                $mapData[$box['x_map']] = [];
            }

            $mapData[$box['x_map']][$box['y_map']] = [
                'file' => $box['box_file'],
                'bonus' => $box['bonus'],
            ];
        }

        return $mapData;
    }

    /**
     * Find map items
     *
     * @param array $repositories Array of repositories
     * @param Player $player Player
     * @param array $borders Border of the map
     *
     * @return array
     */
    public function findItems(array $repositories, Player $player, array $borders)
    {
        if (empty($this->items)) {
            foreach ($repositories as $type => $repo) {
                $qb = $repo->createQueryBuilder('t');
                $qb->select('t, GREATEST(ABS(t.x - :playerX), ABS(t.y - :playerY)) AS distance');
                $qb->where($qb->expr()->between('t.x', $borders['xStart'], $borders['xEnd']));
                $qb->andWhere($qb->expr()->between('t.y', $borders['yStart'], $borders['yEnd']));
                $qb->andWhere('t.map = :map_id');

                if ($type != 'objects') {
                    $qb->andWhere('t.enabled = TRUE');
                }

                $qb->orderBy('distance', 'ASC');
                $qb->setParameter('map_id', $player->getMap()->getId());
                $qb->setParameter('playerX', $player->getX());
                $qb->setParameter('playerY', $player->getY());

                foreach ($qb->getQuery()->getResult() as $result) {
                    $object = $result[0];
                    $this->items[$type][$object->getX()][$object->getY()][] = [
                        'entity' => $object,
                        'distance' => $result['distance'],
                    ];
                }
            }
        }

        return $this->items;
    }

    /**
     * Find map borders, depend on player position
     *
     * @param Player $player Player
     *
     * @return array
     */
    public function findPlayerBorders(Player $player)
    {
        $map = $player->getMap();
        $playerVision = $player->getMapVision();
        $yEnd = $player->getY() + $playerVision;
        $yStart = $player->getY() - $playerVision;
        $xEnd = $player->getX() + $playerVision;
        $xStart = $player->getX() - $playerVision;

        return [
            'xStart' => ($xStart <= 0) ? 1 : $xStart,
            'xEnd' => ($xEnd >= $map->getMaxX()) ? $map->getMaxX() : $xEnd,
            'yStart' => ($yStart <= 0) ? 1 : $yStart,
            'yEnd' => ($yEnd >= $map->getMaxY()) ? $map->getMaxY() : $yEnd,
        ];
    }

    /**
     * Generate random position
     *
     * @param Map $map Map to find a position
     * @param int $xMin X min
     * @param int $xMax X max
     * @param int $yMin Y min
     * @param int $yMax Y max
     * @param int $limit Limit of result
     *
     * @return array
     */
    public function findPosition(Map $map, $xMin, $xMax, $yMin, $yMax, $limit = 1)
    {
        $request = <<<EOT
SELECT
  x,
  y
FROM map_box AS mbx
INNER JOIN map_bonus AS mbs ON (mbx.map_bonus_id = mbs.id)
WHERE mbx.map_id = :map_id
  AND mbs.type IN (:bonus_type)
  AND mbx.x BETWEEN (:x_min) AND (:x_max)
  AND mbx.y BETWEEN (:y_min) AND (:y_max)
ORDER BY random()
LIMIT :limit
EOT;
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('x', 'x');
        $rsm->addScalarResult('y', 'y');

        $query = $this->getEntityManager()->createNativeQuery($request, $rsm);
        $query->setParameters([
            'map_id' => $map->getId(),
            'bonus_type' => [MapBonus::TYPE_DEFAULT, MapBonus::TYPE_RESPAWN],
            'x_min' => (int) $xMin,
            'x_max' => (int) $xMax,
            'y_min' => (int) $yMin,
            'y_max' => (int) $yMax,
            'limit' => (int) $limit,
        ]);

        return ($limit == 1) ? $query->getSingleResult() : $query->getResult();
    }

    /**
     * Has valid position
     *
     * @param Player $player Player
     * @param array $mapBonusTypes Map bonus Type, check if it's different. Default is Impassable
     *
     * @return bool
     */
    public function hasValidPosition(Player $player, array $mapBonusTypes = [MapBonus::TYPE_IMPASSABLE])
    {
        $request = <<<EOT
SELECT mbs.type AS type
FROM map_box AS mbx
INNER JOIN map_bonus AS mbs ON (mbx.map_bonus_id = mbs.id)
WHERE mbx.map_id = :map_id AND mbx.x = :x AND mbx.y = :y
EOT;
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('type', 'type');

        $query = $this->getEntityManager()->createNativeQuery($request, $rsm);
        $map = $player->getMap();
        $query->setParameters([
            'map_id' => $map->getId(),
            'x' => $player->getX(),
            'y' => $player->getY(),
        ]);

        try {
            $result = $query->getSingleResult();

            return !in_array($result['type'], $mapBonusTypes);
        } catch (NoResultException $e) {
            return false;
        }
    }

    /**
     * Get default map
     *
     * @return Map
     */
    public function getDefaultMap()
    {
        if (empty($this->defaultMap)) {
            $this->defaultMap = $this->findOneById(Map::ISLAND);
        }

        return $this->defaultMap;
    }

    /**
     * Add damage on map case
     *
     * @param Map $map Map
     * @param int $x X position
     * @param int $y Y position
     */
    public function takeDamage(Map $map, $x, $y)
    {
        $em = $this->getEntityManager();
        $mapBox = $em->getRepository('DbaGameBundle:MapBox')
            ->findOneBy(
                [
                    'x' => (int) $x,
                    'y' => (int) $y,
                    'map' => $map,
                ]
            );
        $mapBox->setDamage($mapBox->getDamage() + 1);
        $em->persist($mapBox);
        $em->flush();
    }

    /**
     * Generate map for admin generator
     *
     * @param Map $map Map
     * @param int $partX Part X of the map
     * @param int $partY Part y of the map
     *
     * @return array
     */
    public function generateGeneratorMap(Map $map = null, $partX = 0, $partY = 0)
    {
        if (empty($map)) {
            return;
        }

        $request = <<<EOT
SELECT
  mb.x as x_map,
  mb.y as y_map,
  mif.file AS box_file,
  mbs.id AS bonus
FROM map_box AS mb
INNER JOIN map_image_file AS mif ON
  (mb.map_image_id = mif.map_image_id AND mif.damage = 0 AND mif.period = 0)
INNER JOIN map_bonus AS mbs ON (mb.map_bonus_id = mbs.id)
WHERE
  mb.map_id = :map_id
EOT;
        $parameters = [
            'map_id' => $map->getId(),
        ];

        if ($partX !== null || $partY !== null) {
            $request .= <<<EOT
  AND mb.x BETWEEN ((50 * :partX) + 1) AND ((:partX + 1) * 50)
  AND mb.y BETWEEN ((50 * :partY) + 1) AND ((:partY + 1) * 50)
EOT;
            $parameters['partX'] = $partX;
            $parameters['partY'] = $partY;
        }

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('x_map', 'x_map');
        $rsm->addScalarResult('y_map', 'y_map');
        $rsm->addScalarResult('box_file', 'box_file');
        $rsm->addScalarResult('bonus', 'bonus');

        $query = $this->getEntityManager()->createNativeQuery($request, $rsm);
        $query->setParameters($parameters);

        $mapData = [];
        foreach ($query->getResult() as $box) {
            $mapData[$box['x_map']][$box['y_map']] = [
                'image' => $box['box_file'],
                'bonus' => $box['bonus'],
            ];
        }

        return $mapData;
    }

    /**
     * Get available images for generator
     *
     * @return array
     */
    public function getAvailableImages()
    {
        $request = <<<EOT
SELECT
  mi.id AS id,
  mi.name AS name,
  mif.file AS box_file
FROM map_image AS mi
INNER JOIN map_image_file AS mif ON (mi.id = mif.map_image_id AND mif.damage = 0 AND mif.period = 0)
ORDER BY name ASC
EOT;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('box_file', 'box_file');
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('name', 'name');

        $query = $this->getEntityManager()->createNativeQuery($request, $rsm);

        return $query->getResult();
    }
}
