<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Player;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Dba\GameBundle\Services;
use FOS\RestBundle\Controller\FOSRestController;

class BaseController extends FOSRestController
{
    /**
     * @return Services\RepositoryService
     */
    public function repos()
    {
        return $this->get('dba.game.repos');
    }

    /**
     * @return ServicesService
     */
    public function services()
    {
        return $this->get('dba.game.services');
    }

    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->get('logger');
    }

    /**
     * Translate message
     *
     * @param string $message Message
     * @param array $params Message parameters
     * @param string $domain Domain
     *
     * @return string
     */
    public function trans($message, array $params = [], $domain = null)
    {
        return $this->get('translator')->trans($message, $params, $domain);
    }
    /**
     * Get forbidden response
     *
     * @param string $message Optional error message
     *
     * @return Response
     */
    public function forbidden($message = null)
    {
        $response = new JsonResponse([]);
        if (!empty($message)) {
            $response->setData(['error' => $message]);
        }
        $response->setStatusCode(JsonResponse::HTTP_FORBIDDEN);
        return $response;
    }
}
