<?php

namespace Dba\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;

/**
 * @Route("/")
 */
class DefaultController extends BaseController
{
    /**
     * @Route("", name="admin")
     */
    public function indexAction()
    {
        return $this->render('DbaAdminBundle::default/index.html.twig');
    }
}
