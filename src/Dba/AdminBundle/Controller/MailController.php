<?php

namespace Dba\AdminBundle\Controller;

use Dba\GameBundle\Entity\Mail;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/mail")
 */
class MailController extends BaseController
{
    /**
     * @Route("", name="admin.mail")
     */
    public function indexAction(Request $request)
    {
        $mailRepo = $this->repos()->getMailRepository();
        $limitPerPage = 100;

        $page = $request->query->get('page', 1);
        $page = $page < 1 ? 1 : $page;
        $qb = $mailRepo->createQueryBuilder('m');
        $qb->addOrderBy('m.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $limitPerPage)
            ->setMaxResults($limitPerPage);
        $mails = new Paginator($qb, false);

        return $this->render(
            'DbaAdminBundle::mail/index.html.twig',
            [
                'mails' => $mails,
                'limitPerPage' => $limitPerPage,
                'nbPages' => ceil(count($mails) / $limitPerPage),
                'page' => $page,
            ]
        );
    }

    /**
     * @Route("/view/{id}", name="admin.mail.view")
     * @ParamConverter("mail", class="Dba\GameBundle\Entity\Mail")
     */
    public function viewAction(Mail $mail)
    {
        return $this->render(
            'DbaAdminBundle::mail/view.html.twig',
            ['mail' => $mail]
        );
    }
}
