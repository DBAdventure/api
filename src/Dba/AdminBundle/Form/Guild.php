<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Guild extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                Type\TextType::class,
                [
                    'label' => 'form.name',
                ]
            )
            ->add(
                'shortName',
                Type\TextType::class,
                [
                    'label' => 'form.shortName',
                ]
            )
            ->add(
                'history',
                Type\TextareaType::class,
                [
                    'label' => 'form.history',
                ]
            )
            ->add(
                'message',
                Type\TextareaType::class,
                [
                    'label' => 'form.message',
                    'required' => false,
                    'empty_data' => '',
                ]
            )
            ->add(
                'enabled',
                Type\CheckboxType::class,
                [
                    'label' => 'form.enabled',
                    'required' => false,
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
                'data_class' => Entity\Guild::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'guild_admin';
    }
}
