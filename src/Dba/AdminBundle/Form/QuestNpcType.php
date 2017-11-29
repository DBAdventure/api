<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class QuestNpcType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'race',
                Type\ChoiceType::class,
                [
                    'label' => 'form.race',
                    'choices' => array_flip(Entity\Race::NPC_LIST)
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
        return 'quest_npc_type';
    }
}
