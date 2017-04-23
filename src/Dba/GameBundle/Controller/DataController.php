<?php

namespace Dba\GameBundle\Controller;

use DateTime;
use Dba\GameBundle\Form\PlayerRegistration;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Entity\Race;

class DataController extends BaseController
{
    public function getDataAction()
    {
        $playerRepository = $this->repos()->getPlayerRepository();
        return [
            'nbObjects' => $this->repos()->getObjectRepository()->countObjects(),
            'nbBuildings' => $this->repos()->getBuildingRepository()->countBuildings(),
            'nbGuilds' => $this->repos()->getGuildRepository()->countGuilds(),
            'nbActivePlayers' => $playerRepository->countByEnabled(),
            'nbGoodGuys' => $playerRepository->countBySide(Side::GOOD),
            'nbBadGuys' => $playerRepository->countBySide(Side::BAD),
            'nbNpc' => $playerRepository->countBySide(Side::NPC),
            'nbSaiyajins' => $playerRepository->countByRace(Race::SAIYAJIN),
            'nbHumanSaiyajins' => $playerRepository->countByRace(
                Race::HUMAN_SAIYAJIN
            ),
            'nbHumans' => $playerRepository->countByRace(Race::HUMAN),
            'nbNamekians' => $playerRepository->countByRace(Race::NAMEKIAN),
            'nbDragons' => $playerRepository->countByRace(Race::DRAGON),
            'nbAliens' => $playerRepository->countByRace(Race::ALIEN),
            'nbCyborgs' => $playerRepository->countByRace(Race::CYBORG),
            'nbMajins' => $playerRepository->countByRace(Race::MAJIN),
            'onlinePlayers'  => count($playerRepository->getOnlinePlayers()),
        ];
    }

    public function getNewsAction()
    {
        return [
            'news' => $this->repos()->getNewsRepository()->findBy(
                [],
                ['createdAt' => 'DESC']
            ),
        ];
    }

    public function getDataAppearanceAction()
    {
        return [
            Race::HUMAN => [
                'Famille de Bulma' => [
                    'Bulma normal' => 'H.png',
                    'Bulma décontracté' => 'H6.png',
                    'Bulma et bébé trunks' => 'H21.png',
                    'Mère de Bulma' => 'H10.png'
                ],
                'Guerrier(Krilin, Yam...)' => [
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
                'Autres personnages' => [
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
            ],
            Race::HUMAN_SAIYAJIN => [
                'Sangohan' => [
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
                'Trunks' => [
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
                'Sangoten' => [
                    'Sangoten normal' => 'HS18.png',
                    'Sangoten Super Saïyen' => 'HS19.png',
                    'Sangoten GT normal' => 'HS40.png',
                    'Sangoten GT Super Saïyen' => 'HS41.png'
                ],
                'Gotrunks' => [
                    'Gotrunks normal' => 'HS13.png',
                    'Gotrunks Super Saïyen' => 'HS14.png',
                    'Gotrunks Super Saïyen 3' => 'HS15.png',
                    'Gotrunks Super Saïyen 3 + Eclairs' => 'HS35.png'
                ],
                'Pan' => [
                    'Pan' => 'H101.png',
                    'Pan Bébé' => 'HS47.png'
                ],
                'Justicier masqué' => [
                    'Justicier masqué' => 'HS45.png'
                ],
                'Bra' => [
                    'Bra' => 'HS46.png'
                ],
            ],
            Race::NAMEKIAN => [
                'Piccolo' => [
                    'Piccolo enfant' => 'N10.png',
                    'Piccolo' => 'N.png',
                    'Dieuccolo' => 'N1.png'
                ],
                'Tout-puissant' => [
                    'Tout-puissant' => 'N3.png'
                ],
                'Dendé' => [
                    'Dendé jeune' => 'N2.png',
                    'Dendé adolescent' => 'N7.png'
                ],
                'Simple Namek' => [
                    'Namek tunique rouge' => 'N4.png',
                    'Namek tunique bleu' => 'N5.png',
                    'Namek tunique verte' => 'N6.png'
                ],
                'Slug' => [
                    'Slug avec casque' => 'N8.png',
                    'Slug sans casque' => 'N9.png'
                ],
            ],
            RACE::SAIYAJIN => [
                'Sangoku' => [
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
                'Végéta' => [
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
                'Fusion Goku et Végéta' => [
                    'Végéto' => 'S13.png',
                    'Végéto Super Saïyen' => 'S14.png',
                    'Gogéta Super Saïyen 4' => 'S15.png',
                    'Gogéta Super Saïyen 2' => 'S19.png',
                    'Gogéta Super Saïyen 2 + Eclairs' => 'S26.png'
                ],
                'Broly' => [
                    'Broly' => 'S17.png',
                    'Broly Super Saiyen' => 'S21.png',
                    'Broly Super Saiyen 2' => 'S22.png',
                    'Broly Super Saïyen Légendaire' => 'S18.png',
                    'Broly Super Saïyen Légendaire Altern 1' => 'S42.png',
                    'Broly Super Saïyen Légendaire Altern 2' => 'S50.png',
                    'Broly Super Saïyen Légendaire Altern 3' => 'S53.png'
                ],
                'Autres Saïyens' => [
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
                ]
            ],
            Race::ALIEN => [
                'Freezer et sa Famille' => [
                    'Freezer normal' => 'A12.png',
                    'Freezer première transformation' => 'A13.png',
                    'Freezer deuxième transformation' => 'A14.png',
                    'Freezer ultime transformation' => 'A4.png',
                    'Freezer puissance maximale' => 'A30.png',
                    'Metal Freezer' => 'A3.png'
                ],
                'Commando Ginue' => [
                    'Giniue' => 'A7.png',
                    'Guldo' => 'A24.png',
                    'Geece' => 'A10.png',
                    'Barta' => 'A9.png',
                    'Recoom' => 'A8.png'
                ],
                'Serviteur de Freezer' => [
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
                'Baby' => [
                    'Baby' => 'A38.png',
                    'Baby végéta' => 'A15.png',
                    'Baby végéta gorille' => 'A39.png'
                ],
                "Les mercenaires de l'espace" => [
                    'Bojack 1ère transformation' => 'A23.png',
                    'Bojack 2ème transformation' => 'A16.png',
                    'Zangya' => 'A22.png',
                    'Gokua' => 'A29.png',
                    'Bido' => 'A36.png'
                ],
                'Cooler' => [
                    'Cooler' => 'A18.png',
                    'Super Cooler' => 'A17.png',
                    'Metal Cooler' => 'A19.png'
                ],

                'Tapion et son frère' => [
                    'Tapion' => 'A20.png',
                    'Petit frère de Tapion' => 'A21.png'
                ],
                'Garlic' => [
                    'Garlic Junior' => 'A26.png'
                ],
                'Janemba' => [
                    'Janemba1' => 'A27.png',
                    'Janemba2' => 'A28.png'
                ],
                'Autres personnages' => [
                    "Arbitre de l\'autre monde" => 'A31.png',
                    'Catapy' => 'A32.png',
                    'Zold' => 'A33.png',
                    'Tald' => 'A34.png',
                    'Vinegar' => 'A35.png',
                    'Torbie' => 'A37.png',
                    'Rild' => 'A40.png',
                    'Saibaimen' => 'A43.png'
                ],
            ],
            Race::CYBORG => [
                'Les C' => [
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
                'Cell' => [
                    'Cell première transformation' => 'C15.png',
                    'Cell deuxième transformation' => 'C7.png',
                    'Perfect Cell' => 'C1.png',
                    'Reborn Cell' => 'C14.png',
                    'Mini cell' => 'C6.png'
                ],
                'Autres cyborgs' => [
                    'Taopaïppaï' => 'C5.png',
                    'Gil' => 'C16.png'
                ],
            ],
            Race::MAJIN => [
                'Boubou' => [
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
                'Babidi' => [
                    'choice.shape' => '',
                    'Babidi' => 'M2.png'
                ],
                'Serviteurs' => [
                    'choice.shape' => '',
                    'Buïbuï' => 'M3.png',
                    'Sporovich' => 'M7.png',
                    'Yamu' => 'M5.png',
                    'Simple guerrier' => 'M4.png',
                    'Yakon' => 'M6.png'
                ],
            ],
            Race::DRAGON => [
                'Dragon de couleur' => [
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
                'Li Sheron' => [
                    'choice.shape' => '',
                    'Première transformation' => 'D12.png',
                    'Première transformation Altern' => 'D21.png',
                    'Deuxième transformation' => 'D13.png',

                ],
                'Autres personnages' => [
                    'Nova Shenron' => 'D14.png',
                    'Nova Shenron Altern' => 'D20.png',
                    'Uu Shenron' => 'D15.png',
                    'Chii Shenron' => 'D16.png',
                    'Otamine' => 'D17.png',
                    'Haze Shenron' => 'D18.png',
                    '??? Shenron' => 'D19.png'
                ]
            ]
        ];
    }
}
