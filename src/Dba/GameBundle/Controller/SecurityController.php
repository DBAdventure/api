<?php

namespace Dba\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
    }

    /**
     * @Route("/clear", name="clear")
     */
    public function clearAction()
    {
        return new JsonResponse();
    }
}
