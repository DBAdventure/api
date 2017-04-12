<?php

namespace Dba\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/magic")
 */
class MagicController extends BaseController
{
    /**
     * @Route("", name="magic", methods="GET")
     * @Template()
     */
    public function indexAction()
    {
        return $this->render(
            'DbaGameBundle::magic/index.html.twig'
        );
    }
}
