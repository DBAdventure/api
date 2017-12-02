<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NpcObject extends AbstractType
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
                'list',
                Type\CollectionType::class,
                [
                    'entry_type' => Type\TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                ]
            )
            ->add(
                'luck',
                Type\TextType::class,
                [
                    'label' => 'form.luck',
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }

    public function getBlockPrefix()
    {
        return 'npc_object';
    }
}
