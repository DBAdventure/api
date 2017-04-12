<?php

namespace Dba\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin")
 */
class DefaultController extends Controller
{
    /**
     * @Route("", name="admin")
     */
    public function indexAction()
    {
        return $this->render('DbaAdminBundle::default/index.html.twig');
    }
}
