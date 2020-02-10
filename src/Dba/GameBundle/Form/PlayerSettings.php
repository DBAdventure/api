<?php

namespace Dba\GameBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerSettings extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                Type\TextType::class
            )
            ->add(
                'history',
                Type\TextType::class
            )
            ->add(
                'password',
                Type\TextType::class,
                ['required' => false]
            )
            ->add(
                'password_confirm',
                Type\TextType::class,
                ['mapped' => false]
            )
            ->add(
                'email',
                Type\TextType::class
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Entity\Player::class,
                'cascade_validation' => true,
                'csrf_protection' => false,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'player_settings';
    }
}
