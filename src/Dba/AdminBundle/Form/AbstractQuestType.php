<?php

namespace Dba\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AbstractQuestType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            [$this, 'onPostSubmit']
        );
    }

    /**
     * On pre submit event to check
     * quest id
     *
     * @param FormEvent $event Form Event
     */
    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $parent = $form->getParent();
        if (empty($parent)) {
            return;
        }

        if (!method_exists($parent->getData(), 'getOwner')) {
            return;
        }

        $form->getData()->setQuest($parent->getData()->getOwner());
    }
}
