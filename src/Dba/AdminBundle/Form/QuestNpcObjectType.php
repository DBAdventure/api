<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestNpcObjectType extends AbstractQuestType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'npcObject',
                EntityType::class,
                [
                    'class' => Entity\NpcObject::class,
                    'choice_label' => 'name',
                    'label' => 'form.object',
                    'choice_translation_domain' => false,
                ]
            )
            ->add(
                'number',
                Type\TextType::class,
                [
                    'label' => 'form.number',
                ]
            );
        parent::buildForm($builder, $options);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Entity\QuestNpcObject::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'quest_npc_object_type';
    }
}
