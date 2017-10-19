<?php

namespace Dba\AdminBundle\Controller;

use DateTime;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;
use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\Map;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Side;

/**
 * @Route("/npc")
 */
class NpcController extends BaseController
{
    const MAPPING = [
        1 => [
            'fyi' => 'locust',
            'race' => 11,
            'stats' => [
                'skill',
                'agility',
                'accuracy',
                'intellect'
            ]
        ],
        2 => [
            'fyi' => 'crocodile',
            'race' => 12,
            'stats' => [
                'resistance',
                'strength',
                'max_health',
            ]
        ],
        3 => [
            'fyi' => 'wolf',
            'race' => 9,
            'stats' => [
                'resistance',
                'strength',
                'agility'
            ]
        ],
        4 => [
            'fyi' => 'triceratop',
            'race' => 9,
            'stats' => [
                'resistance',
                'strength',
                'max_health',
            ]
        ],
        5 => [
            'fyi' => 'snake.green',
            'race' => 12,
            'stats' => [
                'agility',
                'intellect',
            ]
        ],
        6 => [
            'fyi' => 'snake.purple',
            'race' => 12,
            'stats' => [
                'agility',
                'accuracy',
                'intellect',
            ]
        ],
        7 => [
            'fyi' => 'snake.blue',
            'race' => 12,
            'stats' => [
                'agility',
                'accuracy',
                'intellect',
            ]
        ],
        8 => [
            'fyi' => 'monkey',
            'race' => 9,
            'stats' => [
                'agility',
                'accuracy',
                'resistance'
            ]
        ],
        9 => [
            'fyi' => 'ghost',
            'race' => 10,
            'stats' => [
                'agility',
                'intellect',
                'skill',
                'analysis',
            ]
        ],
        10 => [
            'fyi' => 'soldier',
            'race' => 15,
            'stats' => [
                'strength',
                'agility',
                'max_health',
            ]
        ],
    ];
    /**
     * @Route("", name="admin.npc")
     */
    public function indexAction(Request $request)
    {
        $playerRepo = $this->repos()->getPlayerRepository();
        $limitPerPage = 50;

        $page = $request->query->get('page', 1);
        $page = $page < 1 ? 1 : $page;
        $qb = $playerRepo->createQueryBuilder('p');
        $qb->where(
            $qb->expr()->eq(
                'p.side',
                Side::NPC
            )
        )
            ->addOrderBy('p.name', 'ASC')
            ->setFirstResult(($page - 1) * $limitPerPage)
            ->setMaxResults($limitPerPage);
        $npcs = new Paginator($qb, false);

        return $this->render(
            'DbaAdminBundle::npc/index.html.twig',
            [
                'npcs' => $npcs,
                'limitPerPage' => $limitPerPage,
                'nbPages' => ceil(count($npcs) / $limitPerPage),
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/create", name="admin.npc.create")
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(
            Form\Npc::class,
            []
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            for ($i = 0; $i < $data['number']; $i++) {
                $level = mt_rand(
                    ($data['level'] * 10) + 1,
                    ($data['level'] + 1) * 10
                );
                $npc = $this->createNpc($data['map'], $data['name'] . mt_rand(), $data['type'], $level);
                $this->em()->persist($npc);
            }

            $this->em()->flush();
            $this->addFlash(
                'success',
                $this->trans('npc.created', ['%nb%' => $data['number']])
            );

            return $this->redirect($this->generateUrl('admin.npc'));
        }

        return $this->render(
            'DbaAdminBundle::npc/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    protected function createNpc(Map $map, $name, $type, $level)
    {
        $mapping = self::MAPPING[$type];
        $npc = new Player();
        $npc->setImage($type . '.png');
        $npc->setPassword(
            $this->container->get('security.password_encoder')->encodePassword(
                $npc,
                'test'
            )
        );
        $npc->setZeni(50);
        $npc->setEnabled(true);
        $npc->setLevel($level);
        $npc->setName($name);
        $npc->setUsername($name);
        $npc->setEmail($name . '@dba.com');
        $npc->setRace($this->repos()->getRaceRepository()->findOneById($mapping['race']));
        $npc->setRank(
            $this->repos()->getRankRepository()->findOneBy([
                'race' => $npc->getRace(),
                'level' => 1,
            ])
        );
        $npc->setActionPoints(60);
        $npc->setMovementPoints(100);
        $npc->setFatiguePoints(0);
        $npc->setBattlePoints(0);

        $npc->setSide($this->repos()->getSideRepository()->findOneById(Side::NPC));
        $npc->setSidePoints(0);

        $npc->setIp('127.0.0.1');
        $dateTime = new DateTime();
        $npc->setCreatedAt($dateTime);
        $npc->setUpdatedAt($dateTime);
        $npc->setActionUpdatedAt($dateTime);
        $npc->setKiUpdatedAt($dateTime);
        $npc->setMovementUpdatedAt($dateTime);
        $npc->setFatigueUpdatedAt($dateTime);
        $npc->setMap($map);

        $this->services()->getPlayerService()->respawn($npc);

        $npc->setMaxHealth(500 + (10 * $level));
        $npc->setKi(1);
        $npc->setMaxKi(1);
        $npc->setStrength(1);
        $npc->setResistance(1);
        $npc->setAccuracy(1);
        $npc->setAgility(1);
        $npc->setVision(1);
        $npc->setAnalysis(1);
        $npc->setSkill(1);
        $npc->setIntellect(1);

        $skillPoints = 29 + ($level * 2);
        for ($i = 0; $i < $skillPoints; $i++) {
            $stat = $mapping['stats'][array_rand($mapping['stats'])];
            if ($stat == 'max_health') {
                $npc->setMaxHealth($npc->getMaxHealth() + 40);
                continue;
            }

            $npc->{'set' . ucfirst($stat)}($npc->{'get' . ucfirst($stat)}() + 1);
        }


        $npc->setHealth($npc->getMaxHealth());
        return $npc;
    }
}
