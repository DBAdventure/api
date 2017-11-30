<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Quest extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $finder = new Finder();
        $files = $finder->name('*.png')->in(
            $options['attr']['web-dir'] .
            $options['attr']['asset-path']
        )->exclude('portrait');
        $images = [];
        foreach ($files as $file) {
            $path = str_replace(
                $options['attr']['web-dir'] . $options['attr']['asset-path'] . '/',
                '',
                $file->getPathName()
            );
            $images[$path] = $path;
        }

        $builder
            ->add(
                'name',
                Type\TextType::class,
                [
                    'label' => 'form.name',
                ]
            )
            ->add(
                'npcName',
                Type\TextType::class,
                [
                    'label' => 'form.npcName',
                ]
            )
            ->add(
                'history',
                Type\TextareaType::class,
                [
                    'label' => 'form.history',
                    'required' => false,
                ]
            )
            ->add(
                'image',
                Type\ChoiceType::class,
                [
                    'label' => 'form.image',
                    'choices' => $images,
                    'choice_translation_domain' => false,
                    'attr' => [
                        'data-asset-path' => '/' . $options['attr']['asset-path']
                    ]
                ]
            )
            ->add(
                'requirements',
                Type\CollectionType::class,
                [
                    'entry_type' => RequirementsType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                ]
            )
            ->add(
                'npcNeeded',
                Type\CollectionType::class,
                [
                    'entry_type' => QuestNpcType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                ]
            )
            ->add(
                'npcObjectsNeeded',
                Type\CollectionType::class,
                [
                    'entry_type' => QuestNpcObjectType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                ]
            )
            ->add(
                'objectsNeeded',
                Type\CollectionType::class,
                [
                    'entry_type' => QuestObjectType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                ]
            )
            ->add(
                'gainObjects',
                Type\CollectionType::class,
                [
                    'entry_type' => QuestGainObjectType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
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
            ->add(
                'map',
                EntityType::class,
                [
                    'class' => Entity\Map::class,
                    'choice_label' => 'name',
                    'label' => 'form.map',
                    'choice_translation_domain' => true
                ]
            );
        $integerAttributes = [
            'x',
            'y',
            'gainZeni',
            'gainBattlePoints',
        ];
        foreach ($integerAttributes as $attribute) {
            $builder->add(
                $attribute,
                Type\IntegerType::class,
                [
                    'label' => 'form.' . $attribute,
                ]
            );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Entity\Quest::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'quest';
    }
}
