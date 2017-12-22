<?php

namespace Dba\AdminBundle\Form;

use Dba\GameBundle\Entity;
use Dba\GameBundle\Form\PlayerAppearance;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class Player extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                Type\TextType::class,
                [
                    'label' => 'form.name',
                ]
            )
            ->add(
                'enabled',
                Type\CheckboxType::class,
                [
                    'label' => 'form.enabled',
                ]
            )
            ->add(
                'email',
                Type\TextType::class,
                [
                    'label' => 'form.email',
                ]
            )
            ->add(
                'history',
                Type\TextareaType::class,
                [
                    'label' => 'form.history',
                    'required' => false,
                ]
            )
            ->add(
                'username',
                Type\TextType::class,
                [
                    'label' => 'form.login',
                ]
            )
            ->add(
                'password',
                Type\RepeatedType::class,
                [
                    'required' => false,
                    'first_name' => 'password',
                    'second_name' => 'password_confirm',
                    'type' => Type\PasswordType::class,
                    'options' => [
                        'always_empty' => true,
                    ],
                    'first_options' => [
                        'label' => 'form.password',
                    ],
                    'second_options' => [
                        'label' => 'form.password.confirm',
                    ],
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
            );

        if (in_array($options['role'], [Entity\Player::ROLE_ADMIN, Entity\Player::ROLE_SUPER_ADMIN])) {
            $builder->add(
                'roles',
                Type\ChoiceType::class,
                [
                    'label' => 'form.roles',
                    'choices' => [
                        Entity\Player::ROLE_PLAYER => Entity\Player::ROLE_PLAYER,
                        Entity\Player::ROLE_MODO => Entity\Player::ROLE_MODO,
                        Entity\Player::ROLE_ADMIN => Entity\Player::ROLE_ADMIN,
                    ],
                    'multiple' => true,
                    'choice_translation_domain' => false,
                ]
            );
        }

        $integerAttributes = [
            'x',
            'y',
            'zeni',
            'accuracy',
            'agility',
            'strength',
            'resistance',
            'skill',
            'vision',
            'analysis',
            'intellect',
            'ki',
            'maxKi',
            'health',
            'maxHealth',
            'actionPoints',
            'fatiguePoints',
            'movementPoints',
            'battlePoints',
            'skillPoints'
        ];
        foreach ($integerAttributes as $attribute) {
            $builder->add(
                $attribute,
                Type\IntegerType::class,
                [
                    'label' => 'form.' . $attribute,
                ]
            );
        }

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            array($this, 'onPreSubmit')
        );

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            array($this, 'onPostSetData')
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
                'role' => Entity\Player::ROLE_MODO,
            ]
        );
    }

    /**
     * Block prefix name
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'player';
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

    /**
     * On post set data to remove npc fields
     *
     * @param FormEvent $event Form Event
     */
    public function onPostSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $player = $form->getData();
        if (!$player->isPlayer()) {
            $form->remove('appearance');
            $form->remove('side');
            $form->remove('race');
        }
    }
}
