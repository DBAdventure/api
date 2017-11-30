<?php

namespace Dba\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;
use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\Quest;

/**
 * @Route("/quest")
 */
class QuestController extends BaseController
{
    /**
     * @Route("", name="admin.quest")
     */
    public function indexAction()
    {
        return $this->render(
            'DbaAdminBundle::quest/index.html.twig',
            [
                'quests' => $this->repos()->getQuestRepository()->findBy(
                    [],
                    [
                        'map' => 'ASC',
                        'name' => 'ASC',
                    ]
                ),
            ]
        );
    }

    /**
     * @Route("/create", name="admin.quest.create")
     */
    public function createAction(Request $request)
    {
        $quest = new Quest();
        $form = $this->createForm(
            Form\Quest::class,
            $quest,
            [
                'attr' => [
                    'asset-path' => 'bundles/dbaadmin/images/avatars/npc_quest',
                    'web-dir' => $this->getParameter('kernel.root_dir') . '/../web/',
                ]
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $children = [
                'npcNeeded',
                'npcObjectsNeeded',
                'objectsNeeded',
                'gainObjects',
            ];
            foreach ($children as $child) {
                foreach ($form->get($child)->getData() as $record) {
                    $record->setQuest($quest);
                }
            }

            $this->em()->persist($quest);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('quest.created')
            );

            return $this->redirect($this->generateUrl('admin.quest'));
        }

        return $this->render(
            'DbaAdminBundle::quest/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/edit/{id}", name="admin.quest.edit")
     * @ParamConverter("quest", class="Dba\GameBundle\Entity\Quest")
     */
    public function editAction(Request $request, Quest $quest)
    {
        $form = $this->createForm(
            Form\Quest::class,
            $quest,
            [
                'attr' => [
                    'asset-path' => 'bundles/dbaadmin/images/avatars/npc_quest',
                    'web-dir' => $this->getParameter('kernel.root_dir') . '/../web/',
                ]
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($quest);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('quest.saved')
            );

            return $this->redirect($this->generateUrl('admin.quest'));
        }

        return $this->render(
            'DbaAdminBundle::quest/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/delete/{id}", name="admin.quest.delete")
     * @ParamConverter("quest", class="Dba\GameBundle\Entity\Quest")
     */
    public function deleteAction(Quest $quest)
    {
        $this->em()->remove($quest);
        $this->em()->flush();
        return $this->redirect($this->generateUrl('admin.quest'));
    }
}
