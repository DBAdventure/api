<?php

namespace Dba\GameBundle\Form;

use Dba\GameBundle\Entity;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PlayerAppearance extends AbstractType
{
    protected $serializer;

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addTypeChoice($builder);
        $this->addImageChoice($builder);
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
            ]
        );
    }

    public function getBlockPrefix()
    {
        return 'player_appearance';
    }

    /**
     * Get image type list
     *
     * @param Object $builder
     * @param string $raceId Race id
     *
     * @return array
     */
    protected function addTypeChoice($builder, $raceId = null)
    {
        $result = [
            1 => [
                'choice.group' => '',
                'Famille de Bulma' => 'H1',
                'Guerrier(Krilin, Yam...)' => 'H2',
                'Autres persos' => 'H3'
            ],
            2 => [
                'choice.character' => '',
                'Sangohan' => 'HS1',
                'Trunks' => 'HS2',
                'Sangoten' => 'HS3',
                'Gotrunks' => 'HS4',
                'Pan' => 'HS5',
                'Justicier masqué' => 'HS6',
                'Bra' => 'HS7'
            ],
            3 => [
                'choice.character' => '',
                'Piccolo' => 'N1',
                'Tout-puissant' => 'N2',
                'Dendé' => 'N3',
                'Simple Namek' => 'N4',
                'Slug' => 'N5'
            ],
            4 => [
                'choice.character' => '',
                'Sangoku' => 'S1',
                'Végéta' => 'S2',
                'Fusion Goku et Végéta' => 'S3',
                'Broly' => 'S4',
                'Autres Saïyens' => 'S5'
            ],
            5 => [
                'choice.group' => '',
                'Freezer et sa Famille' => 'A1',
                'Commando Ginue' => 'A2',
                'Serviteur de Freezer' => 'A3',
                'Baby' => 'A4',
                'Les mercenaires de l\'espace' => 'A5',
                'Cooler' => 'A6',
                'Tapion et son frère' => 'A7',
                'Garlic' => 'A8',
                'Janemba' => 'A9',
                'Autres persos' => 'A10'
            ],
            6 => [
                'choice.group' => '',
                'Les C' => 'C1',
                'Cell' => 'C2',
                'Autres cyborgs' => 'C3'
            ],
            7 => [
                'choice.character' => '',
                'Boubou' => 'M1',
                'Babidi' => 'M2',
                'Servant de Babidi' => 'M3'
            ],
            8 => [
                'choice.character' => '',
                'Dragon de couleur' => 'D1',
                'Li Shenron' => 'D2',
                'Autre dragon GT' => 'D3'
            ]
        ];

        $builder->add(
            'type',
            Type\ChoiceType::class,
            [
                'choices' => !empty($result[$raceId]) ? $result[$raceId] : [],
                'choice_translation_domain' => true,
                'mapped' => false,
                'attr' => [
                    'data-list' => $this->getSerializer()->serialize($result, 'json'),
                    'class' => 'hide'
                ]
            ]
        );
    }

    /**
     * Get image list
     *
     * @param $builder
     * @param string $typeId Type id
     *
     * @return array
     */
    protected function addImageChoice($builder, $typeId = null)
    {
        $result = [
            'S1' => [
                'choice.clothes' => '',
                'Sangoku normal' => 'S.png',
                'Sangoku Kaïoken' => 'S35.png',
                'Sangoku Super Saïyen' => 'S2.png',
                'Sangoku Super Saïyen sur Namek' => 'S38.png',
                'Sangoku Blessé sur Namek' => 'S39.png',
                'Sangoku Super Saïyen 2' => 'S1.png',
                'Sangoku revenant de Namek' => 'S5.png',
                'Sangoku Super Saiyen revenant de Namek' => 'S25.png',
                'Sangoku mort' => 'S8.png',
                'Sangoku Super Saïyen 3' => 'S9.png',
                'Sangoku Super Saïyen 3 + Eclairs' => 'S33.png',
                'Sangoku mort Super Saïyen 3' => 'S30.png',
                'Sangoku mort Super Saïyen 3 + Eclairs' => 'S34.png',
                'Sangoku Super Saïyen 3 blessé' => 'S37.png',
                'Sangoku mort Super Saïyen 3 blessé' => 'S36.png',
                'Sangoku Super Saïyen 4' => 'S10.png',
                'Sangoku GT Super Saïyen' => 'S28.png',
                'Sangoku GT Super Saïyen 3' => 'S23.png',
                'Sangoku GT Super Saïyen 3 + Eclairs' => 'S27.png'
            ],
            'S2' => [
                'choice.clothes' => '',
                'Végéta normal' => 'S3.png',
                'Végéta normal habits saïyen' => 'S12.png',
                'Végéta Super Saïyen' => 'S4.png',
                'Végéta Super Saïyen 2' => 'S24.png',
                'Super Végéta' => 'S29.png',
                'Végéta Super Saïyen 4' => 'S11.png',
                'Majin Végéta' => 'S20.png',
                'Végéta mort' => 'S31.png',
                'Végéta mort Super saïyen' => 'S32.png',
                'Prince Végéta' => 'S52.png'
            ],
            'S3' => [
                'choice.clothes' => '',
                'Végéto' => 'S13.png',
                'Végéto Super Saïyen' => 'S14.png',
                'Gogéta Super Saïyen 4' => 'S15.png',
                'Gogéta Super Saïyen 2' => 'S19.png',
                'Gogéta Super Saïyen 2 + Eclairs' => 'S26.png'
            ],
            'S4' => [
                'choice.clothes' => '',
                'Broly' => 'S17.png',
                'Broly Super Saiyen' => 'S21.png',
                'Broly Super Saiyen 2' => 'S22.png',
                'Broly Super Saïyen Légendaire' => 'S18.png',
                'Broly Super Saïyen Légendaire Altern 1' => 'S42.png',
                'Broly Super Saïyen Légendaire Altern 2' => 'S50.png',
                'Broly Super Saïyen Légendaire Altern 3' => 'S53.png'
            ],
            'S5' => [
                'choice.character' => '',
                'Nappa' => 'S7.png',
                'Baddack' => 'S16.png',
                'Raditz' => 'S6.png',
                'Turles' => 'S40.png',
                'Paragus' => 'S41.png',
                'Seripa' => 'S43.png',
                'Seripa gorille' => 'S44.png',
                'Panpukin' => 'S45.png',
                'Toma' => 'S46.png',
                'Toma gorille' => 'S47.png',
                'Sangoku Jr' => 'S48.png',
                'Végéta Jr' => 'S49.png',
                'Roi Végéta' => 'S51.png'
            ],
            'A1' => [
                'choice.character' => '',
                'Freezer normal' => 'A12.png',
                'Freezer première transformation' => 'A13.png',
                'Freezer deuxième transformation' => 'A14.png',
                'Freezer ultime transformation' => 'A4.png',
                'Freezer puissance maximale' => 'A30.png',
                'Metal Freezer' => 'A3.png'
            ],
            'A2' => [
                'choice.character' => '',
                'Giniue' => 'A7.png',
                'Guldo' => 'A24.png',
                'Geece' => 'A10.png',
                'Barta' => 'A9.png',
                'Recoom' => 'A8.png'
            ],
            'A3' => [
                'choice.character' => '',
                'Monstre bleu clair' => 'A.png',
                'Monstre vert clair' => 'A5.png',
                'Monstre vert foncé' => 'A1.png',
                'Monstre marron' => 'A11.png',
                'Monstre marron foncé' => 'A6.png',
                'Zabon' => 'A25.png',
                'Doria' => 'A45.png',
                'Soldat agé' => 'A41.png',
                'Sauza' => 'A42.png',
                'Kiwi' => 'A44.png'
            ],
            'A4' => [
                'choice.character' => '',
                'Baby' => 'A38.png',
                'Baby végéta' => 'A15.png',
                'Baby végéta gorille' => 'A39.png'
            ],
            'A5' => [
                'choice.character' => '',
                'Bojack 1ère transformation' => 'A23.png',
                'Bojack 2ème transformation' => 'A16.png',
                'Zangya' => 'A22.png',
                'Gokua' => 'A29.png',
                'Bido' => 'A36.png'
            ],
            'A6' => [
                'choice.character' => '',
                'Cooler' => 'A18.png',
                'Super Cooler' => 'A17.png',
                'Metal Cooler' => 'A19.png'
            ],
            'A7' => [
                'choice.character' => '',
                'Tapion' => 'A20.png',
                'Petit frère de Tapion' => 'A21.png'
            ],
            'A8' => [
                'choice.character' => '',
                'Garlic Junior' => 'A26.png'
            ],
            'A9' => [
                'choice.character' => '',
                'Janemba1' => 'A27.png',
                'Janemba2' => 'A28.png'
            ],
            'A10.png' => [
                'choice.character' => '',
                'Arbitre de l\'autre monde' => 'A31.png',
                'Catapy' => 'A32.png',
                'Zold' => 'A33.png',
                'Tald' => 'A34.png',
                'Vinegar' => 'A35.png',
                'Torbie' => 'A37.png',
                'Rild' => 'A40.png',
                'Saibaimen' => 'A43.png'
            ],
            'C1' => [
                'choice.character' => '',
                'C-13' => 'C11.png',
                'C-13 Ultime' => 'C10.png',
                'C-14' => 'C17.png',
                'C-15' => 'C12.png',
                'C-16' => 'C.png',
                'C-17' => 'C2.png',
                'Super C-17' => 'C13.png',
                'Super C-17 Altern' => 'C18.png',
                'C-18' => 'C3.png',
                'C-19' => 'C8.png',
                'C-20' => 'C4.png',
                'C-20 sans chapeau' => 'C9.png'
            ],
            'C2' => [
                'choice.character' => '',
                'Cell première transformation' => 'C15.png',
                'Cell deuxième transformation' => 'C7.png',
                'Perfect Cell' => 'C1.png',
                'Reborn Cell' => 'C14.png',
                'Mini cell' => 'C6.png'
            ],
            'C3' => [
                'choice.character' => '',
                'Taopaïppaï' => 'C5.png',
                'Gil' => 'C16.png'
            ],
            'H1' => [
                'choice.character' => '',
                'Bulma normal' => 'H.png',
                'Bulma décontracté' => 'H6.png',
                'Bulma et bébé trunks' => 'H21.png',
                'Mère de Bulma' => 'H10.png'
            ],
            'H2' => [
                'choice.character' => '',
                'Krilin normal' => 'H2.png',
                'Krilin habit saïyen' => 'H13.png',
                'Krilin avec des cheveux' => 'H18.png',
                'Tenshinhan chemise verte' => 'H4.png',
                'Tenshinhan chemise blanche' => 'H23.png',
                'Tenshinhan Période Boubou' => 'H49.png',
                'Yamcha' => 'H3.png',
                'Yamcha habits perso' => 'H48.png',
                'Chaozu' => 'H14.png',
                'Videl cheveux longs' => 'H16.png',
                'Videl cheveux courts' => 'H19.png',
                'Jackie Chun' => 'H47.png'
            ],
            'H3' => [
                'choice.character' => '',
                'Hercule' => 'H5.png',
                'Tortue Génial' => 'H1.png',
                'Plume' => 'H7.png',
                'Yajirobé' => 'H9.png',
                'Yajirobé (un peu plus gros)' => 'H20.png',
                'Mr Popo' => 'H12.png',
                'Maître Karine' => 'H11',
                'Chichi jeune' => 'H25.png',
                'Chichi' => 'H8.png',
                'Gyumao' => 'H24.png',
                'Oolon' => 'H15.png',
                'Maire de la ville' => 'H22.png',
                'Grand père' => 'H26.png',
                'Général Blue' => 'H50.png',
                'Toto le lapin' => 'H51.png',
                'Colonel Silver' => 'H52.png',
                'Arbitre terrien' => 'H53.png',
                'Homme loup' => 'H54.png',
                'Maître des Grues' => 'H55.png'
            ],
            'HS1' => [
                'choice.clothes' => '',
                'Bébé Sangohan' => 'HS5.png',
                'Sangohan + Epée' => 'HS44.png',
                'Sangohan écolier' => 'HS12.png',
                'Sangohan sur namek' => 'HS10.png',
                'Sangohan habit saïyen' => 'HS11.png',
                'Sangohan normal' => 'HS3.png',
                'Sangohan Super Saïyen' => 'HS4.png',
                'Sangohan Super Saïyen 2' => 'HS9.png',
                'Sangohan Super Saïyen 2 + Eclair' => 'HS34.png',
                'Sangohan Super Saïyen habits namek' => 'HS28.png',
                'Sangohan Super Saïyen 2 habits namek' => 'HS27.png',
                'Sangohan Super Saïyen 2 habits namek Altern' => 'HS48.png',
                'Sangohan Super Saïyen 2 habits namek + Eclairs' => 'HS31.png',
                'Sangohan habits Piccolo' => 'HS36.png',
                'Sangohan Super Saïyen habits Piccolo' => 'HS37.png',
                'Sangohan étudiant' => 'HS20.png',
                'Sangohan étudiant Super Saïyen' => 'HS21.png',
                'Sangohan Great Saïyaman + casque' => 'HS22.png',
                'Sangohan Great Saïyaman' => 'HS23.png',
                'Sangohan Great Saïyaman Super Saïyen' => 'HS26.png',
                'Sangohan habits noir de combat' => 'HS25.png',
                'Sangohan Super Saiyen habits noir de combat' => 'HS43.png',
                'Sangohan habits kaïoshin' => 'HS24.png',
                'Sangohan du futur' => 'HS7.png'
            ],
            'HS2' => [
                'choice.clothes' => '',
                'Trunks adolescent' => 'HS6.png',
                'Trunks adolescent + epée' => 'HS33.png',
                'Trunks Super Saïyen + épée' => 'HS8.png',
                'Trunks Adulte' => 'HS.png',
                'Trunks Adulte Super Saïyen' => 'HS2.png',
                'Trunks Adulte Super Saïyen Tenue ville' => 'HS32.png',
                'Trunks Adulte Super Saïyen 2' => 'HS29.png',
                'Super Trunks' => 'HS1.png',
                'Super Trunks Ultime' => 'HS30.png',
                'Super Trunks Ultime + Eclairs' => 'HS42.png',
                'Trunks enfant normal' => 'HS16.png',
                'Trunks enfant Super Saïyen' => 'HS17.png',
                'Trunks GT normal' => 'HS39.png',
                'Trunks GT Super Saïyen' => 'HS38.png'
            ],
            'HS3' => [
                'choice.clothes' => '',
                'Sangoten normal' => 'HS18.png',
                'Sangoten Super Saïyen' => 'HS19.png',
                'Sangoten GT normal' => 'HS40.png',
                'Sangoten GT Super Saïyen' => 'HS41.png'
            ],
            'HS4' => [
                'choice.clothes' => '',
                'Gotrunks normal' => 'HS13.png',
                'Gotrunks Super Saïyen' => 'HS14.png',
                'Gotrunks Super Saïyen 3' => 'HS15.png',
                'Gotrunks Super Saïyen 3 + Eclairs' => 'HS35.png'
            ],
            'HS5' => [
                'choice.clothes' => '',
                'Pan' => 'H101.png',
                'Pan Bébé' => 'HS47.png'
            ],
            'HS6' => [
                'choice.clothes' => '',
                'Justicier masqué' => 'HS45.png'
            ],
            'HS7' => [
                'choice.clothes' => '',
                'Bra' => 'HS46.png'
            ],
            'N1' => [
                'choice.character' => '',
                'Piccolo enfant' => 'N10.png',
                'Piccolo' => 'N.png',
                'Dieuccolo' => 'N1.png'
            ],
            'N2' => [
                'choice.character' => '',
                'Tout-puissant' => 'N3.png'
            ],
            'N3' => [
                'choice.character' => '',
                'Dendé jeune' => 'N2.png',
                'Dendé adolescent' => 'N7.png'
            ],
            'N4' => [
                'choice.character' => '',
                'Namek tunique rouge' => 'N4.png',
                'Namek tunique bleu' => 'N5.png',
                'Namek tunique verte' => 'N6.png'
            ],
            'N5' => [
                'choice.character' => '',
                'Slug avec casque' => 'N8.png',
                'Slug sans casque' => 'N9.png'
            ],
            'M1' => [
                'choice.shape' => '',
                'Boubou' => 'M.png',
                'Bou Mal incarné' => 'M9.png',
                'Majin Bou' => 'M8.png',
                'Majin Bou + Gotrunks' => 'M12.png',
                'Majin Bou + Piccolo' => 'M11',
                'Majin Bou + San Gohan' => 'M10.png',
                'Bou Ultime' => 'M1.png',
                'Super Oub' => 'M13.png'
            ],
            'M2' => [
                'choice.shape' => '',
                'Babidi' => 'M2.png'
            ],
            'M3' => [
                'choice.shape' => '',
                'Buïbuï' => 'M3.png',
                'Sporovich' => 'M7.png',
                'Yamu' => 'M5.png',
                'Simple guerrier' => 'M4.png',
                'Yakon' => 'M6.png'
            ],
            'D1' => [
                'choice.shape' => '',
                'Dragon bleu' => 'D.png',
                'Dragon gris' => 'D1.png',
                'Dragon noir' => 'D2.png',
                'Dragon rouge' => 'D3.png',
                'Dragon violet' => 'D4.png',
                'Dragon bleu très clair' => 'D5.png',
                'Dragon jaune' => 'D6.png',
                'Dragon marron foncé' => 'D7.png',
                'Dragon vert' => 'D8.png',
                'Dragon blanc' => 'D9.png',
                'Dragon rouge foncé' => 'D10.png',
                'Dragon marron clair' => 'D11.png'
            ],
            'D2' => [
                'choice.shape' => '',
                'Première transformation' => 'D12.png',
                'Première transformation Altern' => 'D21.png',
                'Deuxième transformation' => 'D13.png',

            ],
            'D3' => [
                'choice.character' => '',
                'Nova Shenron' => 'D14.png',
                'Nova Shenron Altern' => 'D20.png',
                'Uu Shenron' => 'D15.png',
                'Chii Shenron' => 'D16.png',
                'Otamine' => 'D17.png',
                'Haze Shenron' => 'D18.png',
                '??? Shenron' => 'D19.png'
            ]
        ];
        $builder->add(
            'image',
            Type\ChoiceType::class,
            [
                'choices' => !empty($result[$typeId]) ? $result[$typeId] : [],
                'required' => true,
                'label' => false,
                'attr' => [
                    'data-list' => $this->getSerializer()->serialize($result, 'json'),
                    'class' => 'hide'
                ]
            ]
        );
    }

    /**
     * On pre submit event to check
     * images availability
     *
     * @param FormEvent $event Form Event
     */
    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $player = $form->getData();
        if (!empty($player)) {
            $raceId = $player->getRace()->getId();
        } else {
            $parent = $form->getParent();
            if (empty($parent)) {
                return;
            }

            $raceId = $parent->get('race')->getData()->getId();
        }

        $data = $event->getData();
        $form->remove('type');
        $this->addTypeChoice($form, $raceId);
        if (empty($data['type'])) {
            return;
        }

        $form->remove('image');
        $this->addImageChoice($form, $data['type']);
    }

    /**
     * Get serialize object
     *
     * @return Serializer
     */
    protected function getSerializer()
    {
        if (empty($this->serializer)) {
            $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        }

        return $this->serializer;
    }
}
