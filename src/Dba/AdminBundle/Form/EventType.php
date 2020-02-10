<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class EventType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'message',
                Type\TextareaType::class,
                [
                    'label' => 'form.message',
                    'required' => false,
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
                            $qb->expr()->in(
                                'm.type',
                                [Entity\Map::TYPE_NORMAL]
                            )
                        );

                        return $qb;
                    },
                    'choice_label' => 'name',
                    'label' => 'form.map',
                    'choice_translation_domain' => false,
                    'required' => false,
                ]
            )
            ->add(
                'x',
                Type\IntegerType::class,
                [
                    'label' => 'form.x',
                    'required' => false,
                ]
            )
            ->add(
                'y',
                Type\IntegerType::class,
                [
                    'label' => 'form.y',
                    'required' => false,
                ]
            );
    }

    public function getBlockPrefix()
    {
        return 'event_type';
    }
}
