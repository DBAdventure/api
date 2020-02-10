<?php

namespace Dba\GameBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InboxMessage extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'recipients',
                Type\CollectionType::class,
                [
                    'entry_type' => InboxRecipient::class,
                    'required' => true,
                ]
            )
            ->add(
                'subject',
                Type\TextType::class,
                [
                    'label' => 'form.subject',
                    'required' => true,
                ]
            )
            ->add(
                'message',
                Type\TextareaType::class,
                [
                    'label' => 'form.message',
                    'required' => true,
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Entity\Inbox::class,
                'csrf_protection' => false,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'inbox_message';
    }
}
