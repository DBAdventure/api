<?php

namespace Dba\GameBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Services;
use JMS\Serializer\SerializerBuilder;

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

    /**
     * Get created response
     *
     * @param string $message Optional error message
     *
     * @return Response
     */
    public function createdRequest($message = null)
    {
        $response = new JsonResponse([]);
        if (!empty($message)) {
            $response->setData(['error' => $message]);
        }
        $response->setStatusCode(JsonResponse::HTTP_CREATED);
        return $response;
    }

    /**
     * Get bad request response
     *
     * @param string $message Optional error message
     *
     * @return Response
     */
    public function badRequest($message = null)
    {
        $response = new JsonResponse([]);
        if (!empty($message)) {
            $response->setData(['error' => $message]);
        }
        $response->setStatusCode(JsonResponse::HTTP_BAD_REQUEST);
        return $response;
    }

    /**
     * Get serialize object
     *
     * @return Serializer
     */
    protected function getSerializer()
    {
        if (empty($this->serializer)) {
            $this->serializer = SerializerBuilder::create()->build();
        }

        return $this->serializer;
    }

    protected function getErrorMessages(Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}
