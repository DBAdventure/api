<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class QuestObjectType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'object',
                EntityType::class,
                [
                    'class' => Entity\Object::class,
                    'choice_label' => 'name',
                    'label' => 'form.object',
                    'choice_translation_domain' => false
                ]
            )
            ->add(
                'number',
                Type\TextType::class,
                [
                    'label' => 'form.number'
                ]
            );
    }

    public function getBlockPrefix()
    {
        return 'quest_object_type';
    }
}
