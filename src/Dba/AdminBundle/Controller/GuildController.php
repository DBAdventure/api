<?php

namespace Dba\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Dba\AdminBundle\Controller\BaseController;
use Dba\AdminBundle\Form;
use Dba\GameBundle\Entity\Guild;

/**
 * @Route("/guild")
 */
class GuildController extends BaseController
{
    /**
     * @Route("", name="admin.guild")
     */
    public function indexAction()
    {
        $guildsEnabled = [];
        $guildsDisabled = [];
        $guilds = $this->repos()->getGuildRepository()->findAll();
        foreach ($guilds as $guild) {
            if ($guild->isEnabled()) {
                $guildsEnabled[] = $guild;
                continue;
            }

            $guildsDisabled[] = $guild;
        }

        return $this->render(
            'DbaAdminBundle::guild/index.html.twig',
            [
                'guilds' => [
                    'disabled' => $guildsDisabled,
                    'enabled' => $guildsEnabled,
                ]
            ]
        );
    }

    /**
     * @Route("/edit/{id}", name="admin.guild.edit")
     * @ParamConverter("guild", class="Dba\GameBundle\Entity\Guild")
     */
    public function editAction(Request $request, Guild $guild)
    {
        $form = $this->createForm(
            Form\Guild::class,
            $guild
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // @TODO send mail when guild is enabled or disabled
            // if ($form->get('send-mail')) {
            //     $inbox = new Inbox();
            //     $type = $guild->isEnabled() ? 'enabled' : 'disabled';
            //     $inbox->setSubject($this->trans(sprintf('guild.validation.%s.subject', $type)));
            //     $inbox->setMessage($this->trans(sprintf('guild.validation.%s.message', $type)));
            //     $inbox->setSender($this->getUser());
            // }

            $this->em()->persist($guild);
            $this->em()->flush();

            $this->addFlash(
                'success',
                $this->trans('guild.saved')
            );

            return $this->redirect($this->generateUrl('admin.guild'));
        }

        return $this->render(
            'DbaAdminBundle::guild/edit.html.twig',
            [
                'form' => $form->createView(),
                'guild' => $guild
            ]
        );
    }

    /**
     * @Route("/delete/{id}", name="admin.guild.delete")
     * @ParamConverter("guild", class="Dba\GameBundle\Entity\Guild")
     */
    public function deleteAction(Guild $guild)
    {
        $this->em()->remove($guild);
        $this->em()->flush();
        return $this->redirect($this->generateUrl('admin.guild'));
    }
}
