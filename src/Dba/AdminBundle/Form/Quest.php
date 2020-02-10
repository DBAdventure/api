<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Dba\GameBundle\Services\RepositoryService;
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
                        'data-asset-path' => '/' . $options['attr']['asset-path'],
                    ],
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
                'onAccepted',
                EventType::class,
                [
                    'required' => false,
                    'label' => 'form.teleport',
                ]
            )
            ->add(
                'onCompleted',
                EventType::class,
                [
                    'required' => false,
                    'label' => 'form.teleport',
                ]
            )
            ->add(
                'onFinished',
                EventType::class,
                [
                    'required' => false,
                    'label' => 'form.teleport',
                ]
            )
            ->add(
                'npcsNeeded',
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
                    'choice_translation_domain' => false,
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
        $builder->get('requirements')
            ->addModelTransformer(
                new CallbackTransformer(
                    function ($bonus) {
                        if (empty($bonus)) {
                            return [];
                        }

                        $data = [];
                        foreach ($bonus as $key => $value) {
                            $data[$key] = [
                                'key' => $key,
                                'value' => $value,
                            ];
                        }

                        return $data;
                    },
                    function ($data) {
                        if (empty($data)) {
                            return [];
                        }

                        $result = [];
                        foreach ($data as $bonus) {
                            $result[$bonus['key']] = $bonus['value'];
                        }

                        return $result;
                    }
                )
            );

        foreach (['onAccepted', 'onCompleted', 'onFinished'] as $eventType) {
            $builder->get($eventType)
                ->addModelTransformer(
                    $this->getEventTransformer($options['repositories'])
                );
        }
    }

    /**
     * Event transformer
     *
     * @param RepositoryService $em Entity Manager
     *
     * @return CallbackTransformer
     */
    protected function getEventTransformer(RepositoryService $em)
    {
        return new CallbackTransformer(
            function ($event) use ($em) {
                if (empty($event)) {
                    return [];
                }

                if (!empty($event['map'])) {
                    $event['map'] = $em->getMapRepository()->findOneById($event['map']);
                }

                return $event;
            },
            function ($data) use ($em) {
                if (empty($data)) {
                    return [];
                }

                if (!empty($data['map'])) {
                    $data['map'] = $data['map']->getId();
                }

                return $data;
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('repositories');
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
