<?php

namespace Dba\GameBundle\Form;

use Doctrine\ORM\EntityRepository;
use Dba\GameBundle\Entity;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class PlayerRegistration extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'username',
                Type\TextType::class,
                [
                    'label' => 'form.login',
                ]
            )
            ->add(
                'name',
                Type\TextType::class,
                [
                    'label' => 'form.name',
                ]
            )
            ->add(
                'password',
                Type\RepeatedType::class,
                [
                    'first_name' => 'password',
                    'second_name' => 'password_confirm',
                    'type' => Type\PasswordType::class,
                    'first_options' => [
                        'label' => 'form.password',
                    ],
                    'second_options' => [
                        'label' => 'form.password.confirm',
                    ],
                ]
            )
            ->add(
                'email',
                Type\RepeatedType::class,
                [
                    'first_name' => 'email',
                    'second_name' => 'email_confirm',
                    'type' => Type\EmailType::class,
                    'first_options' => [
                        'label' => 'form.email',
                    ],
                    'second_options' => [
                        'label' => 'form.email.confirm',
                    ],
                ]
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
                        'ranger' => 6
                    ],
                    'label' => 'form.class',
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
                    'choice_label' => 'name',
                    'label' => 'form.race',
                    'choice_translation_domain' => true
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
                    'choice_label' => 'name',
                    'label' => 'form.side',
                    'choice_translation_domain' => true
                ]
            )
            ->add(
                'appearance',
                PlayerAppearance::class,
                [
                    'mapped' => false
                ]
            );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            array($this, 'onPreSubmit')
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
