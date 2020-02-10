<?php

namespace Dba\GameBundle\Form;

use Dba\GameBundle\Entity;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlayerRegistration extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                Type\TextType::class
            )
            ->add(
                'name',
                Type\TextType::class
            )
            ->add(
                'password',
                Type\TextType::class
            )
            ->add(
                'password_confirm',
                Type\TextType::class,
                ['mapped' => false]
            )
            ->add(
                'email',
                Type\TextType::class
            )
            ->add(
                'email_confirm',
                Type\TextType::class,
                ['mapped' => false]
            )
            ->add(
                'class',
                Type\ChoiceType::class,
                [
                    'choices' => [
                        'warrior' => 1,
                        'magus' => 2,
                        'thief' => 3,
                        'healer' => 4,
                        'analyst' => 5,
                        'ranger' => 6,
                    ],
                    'mapped' => false,
                ]
            )
            ->add(
                'race',
                EntityType::class,
                [
                    'class' => Entity\Race::class,
                    'query_builder' => function (EntityRepository $er) {
                        $qb = $er->createQueryBuilder('r');
                        $qb->where(
                            $qb->expr()->in(
                                'r.id',
                                [
                                    Entity\Race::HUMAN,
                                    Entity\Race::HUMAN_SAIYAJIN,
                                    Entity\Race::NAMEKIAN,
                                    Entity\Race::SAIYAJIN,
                                    Entity\Race::ALIEN,
                                    Entity\Race::CYBORG,
                                    Entity\Race::MAJIN,
                                    Entity\Race::DRAGON,
                                ]
                            )
                        );

                        return $qb;
                    },
                ]
            )
            ->add(
                'side',
                EntityType::class,
                [
                    'class' => Entity\Side::class,
                    'query_builder' => function (EntityRepository $er) {
                        $qb = $er->createQueryBuilder('s');
                        $qb->where(
                            $qb->expr()->in(
                                's.id',
                                [Entity\Side::GOOD, Entity\Side::BAD]
                            )
                        );

                        return $qb;
                    },
                ]
            )
            ->add(
                'appearance',
                PlayerAppearance::class,
                [
                    'mapped' => false,
                ]
            );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            [$this, 'onPreSubmit']
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Entity\Player::class,
                'cascade_validation' => true,
                'csrf_protection' => false,
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'player_registration';
    }

    /**
     * On pre submit event to check
     * images availability
     *
     * @param FormEvent $event Form Event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $player = $event->getForm()->getData();
        $data = $event->getData();
        if (!empty($data['appearance']['image'])) {
            $player->setImage($data['appearance']['image']);
        }
    }
}
