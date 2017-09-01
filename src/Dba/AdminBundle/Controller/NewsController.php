<?php

namespace Dba\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;
use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\News;

/**
 * @Route("/news")
 */
class NewsController extends BaseController
{
    /**
     * @Route("", name="admin.news")
     */
    public function indexAction()
    {
        return $this->render(
            'DbaAdminBundle::news/index.html.twig',
            [
                'newsList' => $this->repos()->getNewsRepository()->findBy(
                    [],
                    ['createdAt' => 'DESC']
                ),
            ]
        );
    }

    /**
     * @Route("/create", name="admin.news.create")
     */
    public function createAction(Request $request)
    {
        $news = new News();
        $form = $this->createForm(
            Form\News::class,
            $news,
            [
                'attr' => [
                    'asset-path' => 'bundles/dbagame/images/avatars',
                    'web-dir' => $this->getParameter('kernel.root_dir') . '/../web/',
                ]
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $news->setCreatedBy($this->getUser());

            $this->em()->persist($news);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('news.created')
            );

            return $this->redirect($this->generateUrl('admin.news'));
        }

        return $this->render(
            'DbaAdminBundle::news/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/edit/{id}", name="admin.news.edit")
     * @ParamConverter("news", class="Dba\GameBundle\Entity\News")
     */
    public function editAction(Request $request, News $news)
    {
        $form = $this->createForm(
            Form\News::class,
            $news,
            [
                'attr' => [
                    'asset-path' => 'bundles/dbagame/images/avatars',
                    'web-dir' => $this->getParameter('kernel.root_dir') . '/../web/',
                ]
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em()->persist($news);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('news.saved')
            );

            return $this->redirect($this->generateUrl('admin.news'));
        }

        return $this->render(
            'DbaAdminBundle::news/form.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/delete/{id}", name="admin.news.delete")
     * @ParamConverter("news", class="Dba\GameBundle\Entity\News")
     */
    public function deleteAction(News $news)
    {
        $this->em()->remove($news);
        $this->em()->flush();
        return $this->redirect($this->generateUrl('admin.news'));
    }
}
