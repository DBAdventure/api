<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class News extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $finder = new Finder();
        $files = $finder->name('*.png')->in(
            $options['attr']['web-dir'] .
            $options['attr']['asset-path'] .
            '/{npc,npc_quest,players}'
        );
        $images = [];
        foreach ($files as $file) {
            $images[basename($file)] = str_replace(
                $options['attr']['web-dir'],
                '/',
                $file->getPathName()
            );
        }

        $builder
            ->add(
                'subject',
                Type\TextType::class,
                [
                    'label' => 'form.subject',
                ]
            )
            ->add(
                'message',
                Type\TextareaType::class,
                [
                    'label' => 'form.message',
                ]
            )
            ->add(
                'image',
                Type\ChoiceType::class,
                [
                    'label' => 'form.image',
                    'choices' => $images,
                    'choice_translation_domain' => false
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
                'data_class' => Entity\News::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'news';
    }
}
