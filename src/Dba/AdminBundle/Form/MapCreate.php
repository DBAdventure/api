<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapCreate extends AbstractType
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
                'maxX',
                Type\IntegerType::class,
                [
                    'label' => 'form.max.x',
                    'data' => 20,
                ]
            )
            ->add(
                'maxY',
                Type\IntegerType::class,
                [
                    'label' => 'form.max.y',
                    'data' => 20,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Entity\Map::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'map_create';
    }
}
