<?php

namespace Dba\AdminBundle\Controller;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;
use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\MapObject;
use Dba\GameBundle\Entity\MapObjectType;

/**
 * @Route("/map-object")
 */
class MapObjectController extends BaseController
{
    /**
     * @Route("", name="admin.map.object")
     */
    public function indexAction(Request $request)
    {
        $limitPerPage = 50;
        $page = $request->query->get('page', 1);
        $page = $page < 1 ? 1 : $page;
        $qb = $this->repos()->getMapObjectRepository()->createQueryBuilder('mot');

        $type = $request->query->get('type', null);
        if (!empty($type) && in_array($type, MapObjectType::TYPE_LIST)) {
            $qb->where(
                $qb->expr()->eq(
                    'mot.mapObjectType',
                    array_search($type, MapObjectType::TYPE_LIST)
                )
            );
        }

        $qb->addOrderBy('mot.map', 'ASC')
            ->setFirstResult(($page - 1) * $limitPerPage)
            ->setMaxResults($limitPerPage);
        $mapObjects = new Paginator($qb, false);

        return $this->render(
            'DbaAdminBundle::map-object/index.html.twig',
            [
                'mapObjectList' => $mapObjects,
                'limitPerPage' => $limitPerPage,
                'nbPages' => ceil(count($mapObjects) / $limitPerPage),
                'page' => $page,
                'types' => MapObjectType::TYPE_LIST,
                'type' => $type,
            ]
        );
    }

    /**
     * @Route("/create", name="admin.map.object.create")
     */
    public function createAction(Request $request)
    {
        $mapObject = new MapObject();
        $form = $this->createForm(
            Form\MapObject::class,
            $mapObject
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($mapObject);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('map.object.created')
            );

            return $this->redirect($this->generateUrl('admin.map.object'));
        }

        return $this->render(
            'DbaAdminBundle::map-object/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/edit/{id}", name="admin.map.object.edit")
     * @ParamConverter("mapObject", class="Dba\GameBundle\Entity\MapObject")
     */
    public function editAction(Request $request, MapObject $mapObject)
    {
        $form = $this->createForm(
            Form\MapObject::class,
            $mapObject
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($mapObject);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('map.object.saved')
            );

            return $this->redirect($this->generateUrl('admin.map.object'));
        }

        return $this->render(
            'DbaAdminBundle::map-object/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/delete/{id}", name="admin.map.object.delete")
     * @ParamConverter("mapObject", class="Dba\GameBundle\Entity\MapObject")
     */
    public function deleteAction(MapObject $mapObject)
    {
        $this->em()->remove($mapObject);
        $this->em()->flush();
        return $this->redirect($this->generateUrl('admin.map.object'));
    }
}
