<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestNpcType extends AbstractQuestType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'race',
                EntityType::class,
                [
                    'class' => Entity\Race::class,
                    'label' => 'form.race',
                    'choice_label' => 'name',
                    'label' => 'form.race',
                    'query_builder' => function (EntityRepository $er) {
                        $qb = $er->createQueryBuilder('r');
                        $qb->where(
                            $qb->expr()->in(
                                'r.id',
                                array_flip(Entity\Race::NPC_LIST)
                            )
                        );
                        return $qb;
                    },
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
                'data_class' => Entity\QuestNpc::class,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'quest_npc_type';
    }
}
