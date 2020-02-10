<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NpcObject extends AbstractType
{
    /**
     * {@inheritdoc}
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
                'races',
                Type\CollectionType::class,
                [
                    'entry_type' => EntityType::class,
                    'entry_options' => [
                        'class' => Entity\Race::class,
                        'choice_label' => 'name',
                        'query_builder' => function (EntityRepository $er) {
                            $qb = $er->createQueryBuilder('r');
                            $qb->where(
                                $qb->expr()->notIn(
                                    'r.id',
                                    [
                                        Entity\Race::HUMAN,
                                        Entity\Race::HUMAN_SAIYAJIN,
                                        Entity\Race::NAMEKIAN,
                                        Entity\Race::SAIYAJIN,
                                        Entity\Race::ALIEN,
                                        Entity\Race::CYBORG,
                                        Entity\Race::MAJIN,
                                        Entity\Race::DRAGON,
                                    ]
                                )
                            );

                            return $qb;
                        },
                        'choice_translation_domain' => false,
                    ],
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
        $resolver->setDefaults(
            [
                'data_class' => Entity\NpcObject::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'npc_object';
    }
}
