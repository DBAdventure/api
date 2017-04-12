<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Npc extends AbstractType
{
    const TYPES = [
        1 => 'locust',
        2 => 'crocodile',
        3 => 'wolf',
        4 => 'triceratop',
        5 => 'snake.green',
        6 => 'snake.purple',
        7 => 'snake.blue',
        8 => 'monkey',
        9 => 'ghost',
        10 => 'soldier'
    ];

    const LEVELS = [
        '0 - 10',
        '11 - 20',
        '21 - 30',
        '31 - 30',
        '41 - 30',
        '51 - 30',
        '61 - 30',
        '71 - 30',
        '81 - 30',
        '91 - 100'
    ];

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
                'type',
                Type\ChoiceType::class,
                [
                    'label' => 'form.type',
                    'choices' => array_flip(self::TYPES),
                ]
            )
            ->add(
                'number',
                Type\TextType::class,
                [
                    'label' => 'form.number',
                ]
            )
            ->add(
                'level',
                Type\ChoiceType::class,
                [
                    'label' => 'form.level',
                    'choices' => array_flip(self::LEVELS),
                    'choice_translation_domain' => false
                ]
            )
            ->add(
                'map',
                EntityType::class,
                [
                    'class' => Entity\Map::class,
                    'query_builder' => function (EntityRepository $er) {
                        $qb = $er->createQueryBuilder('m');
                        $qb->where(
                            $qb->expr()->notIn(
                                'm.id',
                                [Entity\Map::HELL, Entity\Map::HEAVEN]
                            )
                        );
                        return $qb;
                    },
                    'choice_label' => 'name',
                    'label' => 'form.map',
                    'choice_translation_domain' => true
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
        return 'npc';
    }
}
