<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapObject extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'mapObjectType',
                EntityType::class,
                [
                    'class' => Entity\MapObjectType::class,
                    'choice_label' => 'name',
                    'label' => 'form.type',
                    'choice_translation_domain' => true,
                ]
            )
            ->add(
                'map',
                EntityType::class,
                [
                    'class' => Entity\Map::class,
                    'choice_label' => 'name',
                    'label' => 'form.map',
                    'choice_translation_domain' => false
                ]
            )
            ->add(
                'x',
                Type\IntegerType::class,
                [
                    'label' => 'form.x',
                ]
            )
            ->add(
                'y',
                Type\IntegerType::class,
                [
                    'label' => 'form.y',
                ]
            )
            ->add(
                'number',
                Type\IntegerType::class,
                [
                    'label' => 'form.number',
                    'required' => false
                ]
            )
            ->add(
                'object',
                EntityType::class,
                [
                    'class' => Entity\Object::class,
                    'choice_label' => 'name',
                    'label' => 'form.object',
                    'choice_translation_domain' => false,
                    'required' => false,
                ]
            )
            ->add(
                'extra',
                Type\CollectionType::class,
                [
                    'entry_type' => MapObjectExtraType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
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
                'data_class' => Entity\MapObject::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'map_object';
    }
}
