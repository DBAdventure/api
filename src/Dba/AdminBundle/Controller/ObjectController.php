<?php

namespace Dba\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;
use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\Object;

/**
 * @Route("/object")
 */
class ObjectController extends BaseController
{
    /**
     * @Route("", name="admin.object")
     */
    public function indexAction()
    {
        return $this->render(
            'DbaAdminBundle::object/index.html.twig',
            [
                'objectList' => $this->repos()->getObjectRepository()->findBy(
                    [],
                    [
                        'type' => 'ASC',
                        'price' => 'ASC'
                    ]
                ),
            ]
        );
    }

    /**
     * @Route("/create", name="admin.object.create")
     */
    public function createAction(Request $request)
    {
        $object = new Object();
        $form = $this->createForm(
            Form\Object::class,
            $object,
            [
                'attr' => [
                    'asset-path' => 'bundles/dbagame/images/objects',
                    'web-dir' => $this->getParameter('kernel.root_dir') . '/../web/',
                ]
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($object);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('object.created')
            );

            return $this->redirect($this->generateUrl('admin.object'));
        }

        return $this->render(
            'DbaAdminBundle::object/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/edit/{id}", name="admin.object.edit")
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object")
     */
    public function editAction(Request $request, Object $object)
    {
        $form = $this->createForm(
            Form\Object::class,
            $object,
            [
                'attr' => [
                    'asset-path' => 'bundles/dbagame/images/objects',
                    'web-dir' => $this->getParameter('kernel.root_dir') . '/../web/',
                ]
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($object);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('object.saved')
            );

            return $this->redirect($this->generateUrl('admin.object'));
        }

        return $this->render(
            'DbaAdminBundle::object/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/delete/{id}", name="admin.object.delete")
     * @ParamConverter("object", class="Dba\GameBundle\Entity\Object")
     */
    public function deleteAction(Object $object)
    {
        $this->em()->remove($object);
        $this->em()->flush();
        return $this->redirect($this->generateUrl('admin.object'));
    }
}
