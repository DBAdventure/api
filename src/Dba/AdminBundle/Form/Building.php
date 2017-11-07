<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Building extends AbstractType
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
        )->exclude('map');
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
                'map',
                EntityType::class,
                [
                    'class' => Entity\Map::class,
                    'choice_label' => 'name',
                    'label' => 'form.map',
                    'choice_translation_domain' => true
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
                'type',
                Type\ChoiceType::class,
                [
                    'label' => 'form.type',
                    'choices' => array_flip(Entity\Building::TYPE_LIST),
                    'choice_translation_domain' => false,
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
                'data_class' => Entity\Building::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'building';
    }
}
