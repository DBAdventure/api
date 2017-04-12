<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Object extends AbstractType
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
                'weight',
                Type\IntegerType::class,
                [
                    'label' => 'form.weight',
                ]
            )
            ->add(
                'price',
                Type\IntegerType::class,
                [
                    'label' => 'form.price',
                ]
            )
            ->add(
                'type',
                Type\ChoiceType::class,
                [
                    'label' => 'form.type',
                    'choices' => array_flip(Entity\Object::TYPE_LIST),
                    'choice_translation_domain' => false,
                ]
            )
            ->add(
                'bonus',
                Type\CollectionType::class,
                [
                    'entry_type' => BonusType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
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
        ;

        $builder->get('bonus')
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
                                'value' => $value
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
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Entity\Object::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'object';
    }
}
