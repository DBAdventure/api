<?php

namespace Dba\AdminBundle\Controller;

use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\NpcObject;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/quest-object")
 */
class QuestObjectController extends BaseController
{
    /**
     * @Route("", name="admin.quest.object")
     */
    public function indexAction()
    {
        return $this->render(
            'DbaAdminBundle::quest-object/index.html.twig',
            [
                'quests' => $this->repos()->getNpcObjectRepository()->findBy(
                    [],
                    [
                        'name' => 'ASC',
                    ]
                ),
            ]
        );
    }

    /**
     * @Route("/create", name="admin.quest.object.create")
     */
    public function createAction(Request $request)
    {
        $questNpcObject = new NpcObject();
        $form = $this->createForm(
            Form\NpcObject::class,
            $questNpcObject
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $questNpcObject->removeDuplicates();
            $this->em()->persist($questNpcObject);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('quest.object.created')
            );

            return $this->redirect($this->generateUrl('admin.quest.object'));
        }

        return $this->render(
            'DbaAdminBundle::quest-object/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/edit/{id}", name="admin.quest.object.edit")
     * @ParamConverter("quest", class="Dba\GameBundle\Entity\NpcObject")
     */
    public function editAction(Request $request, NpcObject $questNpcObject)
    {
        $form = $this->createForm(
            Form\NpcObject::class,
            $questNpcObject
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $questNpcObject->removeDuplicates();
            $this->em()->persist($questNpcObject);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('quest.object.saved')
            );

            return $this->redirect($this->generateUrl('admin.quest.object'));
        }

        return $this->render(
            'DbaAdminBundle::quest-object/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/delete/{id}", name="admin.quest.object.delete")
     * @ParamConverter("quest", class="Dba\GameBundle\Entity\NpcObject")
     */
    public function deleteAction(NpcObject $questNpcObject)
    {
        $this->em()->remove($questNpcObject);
        $this->em()->flush();

        return $this->redirect($this->generateUrl('admin.quest'));
    }
}
