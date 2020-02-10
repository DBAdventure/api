<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;

class BonusType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'key',
                Type\ChoiceType::class,
                [
                    'label' => 'form.key',
                    'choices' => array_flip(Entity\GameObject::BONUS_LIST),
                ]
            )
            ->add(
                'value',
                Type\TextType::class,
                [
                    'label' => 'form.value',
                ]
            );
    }

    public function getBlockPrefix()
    {
        return 'bonus_type';
    }
}
