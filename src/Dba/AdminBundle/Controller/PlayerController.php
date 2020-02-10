<?php

namespace Dba\AdminBundle\Controller;

use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Side;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/player")
 */
class PlayerController extends BaseController
{
    /**
     * @Route("", name="admin.player")
     */
    public function indexAction(Request $request)
    {
        $playerRepo = $this->repos()->getPlayerRepository();
        $limitPerPage = 50;

        $page = $request->query->get('page', 1);
        $page = $page < 1 ? 1 : $page;
        $qb = $playerRepo->createQueryBuilder('p');
        $qb->where(
            $qb->expr()->in(
                'p.side',
                [Side::GOOD, Side::BAD]
            )
        )
            ->addOrderBy('p.name', 'ASC')
            ->setFirstResult(($page - 1) * $limitPerPage)
            ->setMaxResults($limitPerPage);
        $players = new Paginator($qb, false);

        return $this->render(
            'DbaAdminBundle::player/index.html.twig',
            [
                'players' => $players,
                'limitPerPage' => $limitPerPage,
                'nbPages' => ceil(count($players) / $limitPerPage),
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/search", name="admin.player.search", methods="POST")
     */
    public function searchAction(Request $request)
    {
        $who = $request->request->get('who');
        $player = $this->repos()->getPlayerRepository()->findOneByName($who);
        if (empty($player)) {
            $this->addFlash(
                'danger',
                $this->trans(
                    'player.not.found',
                    ['%name%' => $who]
                )
            );

            return $this->redirect($this->generateUrl('admin.player'));
        }

        return $this->redirect($this->generateUrl('admin.player.edit', ['id' => $player->getId()]));
    }

    /**
     * @Route("/edit/{id}", name="admin.player.edit")
     * @ParamConverter("player", class="Dba\GameBundle\Entity\Player")
     */
    public function editAction(Request $request, Player $player)
    {
        $roleService = $this->get('dba.admin.role');
        if (!$roleService->isGranted(Player::ROLE_SUPER_ADMIN, $this->getUser()) &&
            $this->getUser()->getId() != $player->getId() &&
            $roleService->isGranted(Player::ROLE_ADMIN, $player)
        ) {
            $this->addFlash(
                'danger',
                $this->trans('player.cant.edit')
            );

            return $this->redirect($this->generateUrl('admin.player'));
        }

        $form = $this->createForm(
            Form\Player::class,
            $player,
            ['role' => $this->getUser()->getRoles()[0]]
        );

        $oldPassword = $player->getPassword();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($player->getPassword())) {
                $player->setPassword($oldPassword);
            } else {
                $player->setPassword(
                    $this->container->get('security.password_encoder')->encodePassword(
                        $player,
                        $player->getPassword()
                    )
                );
            }
            $this->em()->persist($player);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('player.edited')
            );

            return $this->redirect($this->generateUrl('admin.player.edit', ['id' => $player->getId()]));
        }

        return $this->render(
            'DbaAdminBundle::player/edit.html.twig',
            [
                'form' => $form->createView(),
                'player' => $player,
            ]
        );
    }

    /**
     * @Route("/delete/{id}", name="admin.player.delete")
     * @ParamConverter("player", class="Dba\GameBundle\Entity\Player")
     */
    public function deleteAction(Player $player)
    {
        // Do not remove, add this player as deleted status
        $side = $player->getSide()->getId();
        $this->em()->remove($player);
        $this->em()->flush();

        if ($side === Side::NPC) {
            return $this->redirect($this->generateUrl('admin.npc'));
        }

        return $this->redirect($this->generateUrl('admin.player'));
    }
}
