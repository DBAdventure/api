<?php

namespace Dba\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;
use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\Building;

/**
 * @Route("/building")
 */
class BuildingController extends BaseController
{
    /**
     * @Route("", name="admin.building")
     */
    public function indexAction()
    {
        return $this->render(
            'DbaAdminBundle::building/index.html.twig',
            [
                'buildingList' => $this->repos()->getBuildingRepository()->findBy(
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
     * @Route("/create", name="admin.building.create")
     */
    public function createAction(Request $request)
    {
        $building = new Building();
        $form = $this->createForm(
            Form\Building::class,
            $building,
            [
                'attr' => [
                    'asset-path' => 'bundles/dbaadmin/images/buildings',
                    'web-dir' => $this->getParameter('kernel.root_dir') . '/../web/',
                ]
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($building);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('building.created')
            );

            return $this->redirect($this->generateUrl('admin.building'));
        }

        return $this->render(
            'DbaAdminBundle::building/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/edit/{id}", name="admin.building.edit")
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     */
    public function editAction(Request $request, Building $building)
    {
        $form = $this->createForm(
            Form\Building::class,
            $building,
            [
                'attr' => [
                    'asset-path' => 'bundles/dbaadmin/images/buildings',
                    'web-dir' => $this->getParameter('kernel.root_dir') . '/../web/',
                ]
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($building);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('building.saved')
            );

            return $this->redirect($this->generateUrl('admin.building'));
        }

        return $this->render(
            'DbaAdminBundle::building/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/delete/{id}", name="admin.building.delete")
     * @ParamConverter("building", class="Dba\GameBundle\Entity\Building")
     */
    public function deleteAction(Building $building)
    {
        $this->em()->remove($building);
        $this->em()->flush();
        return $this->redirect($this->generateUrl('admin.building'));
    }
}
