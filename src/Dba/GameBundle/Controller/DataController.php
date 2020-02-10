<?php

namespace Dba\GameBundle\Controller;

use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\Race;
use Dba\GameBundle\Entity\Side;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\Controller\Annotations;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Annotations\NamePrefix("data_")
 */
class DataController extends BaseController
{
    const LIMIT_PER_PAGE = 60;

    /**
     * @Annotations\Get("/game")
     */
    public function getGameAction()
    {
        $playerRepository = $this->repos()->getPlayerRepository();
        $data = [
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
        ];
        if ($this->getUser()) {
            $data['unreadMessages'] = $this->repos()->getInboxRepository()->countUnreadMessages($this->getUser());
        }

        return $data;
    }

    /**
     * @Annotations\Get("/online/players")
     */
    public function getOnlinePlayerAction()
    {
        $playerRepository = $this->repos()->getPlayerRepository();

        return ['nbOnlinePlayers' => count($playerRepository->getOnlinePlayers())];
    }

    /**
     * @Annotations\Get("/news")
     */
    public function getNewsAction()
    {
        return [
            'news' => $this->repos()->getNewsRepository()->findBy(
                ['enabled' => true],
                ['createdAt' => 'DESC']
            ),
        ];
    }

    /**
     * @ParamConverter("player", class="Dba\GameBundle\Entity\Player")
     * @Annotations\Get("/player/{player}", name="_player_info")
     */
    public function getPlayerInfoAction(Player $player)
    {
        return [
            'player' => $player,
        ];
    }

    /**
     * @Annotations\Get("/appearance")
     */
    public function getAppearanceAction()
    {
        return [
            Race::HUMAN => [
                'H1' => [
                    'label' => 'Famille de Bulma',
                    'value' => [
                        'Bulma normal' => 'H.png',
                        'Bulma décontracté' => 'H6.png',
                        'Bulma et bébé trunks' => 'H21.png',
                        'Mère de Bulma' => 'H10.png',
                    ],
                ],
                'H2' => [
                    'label' => 'Guerrier(Krilin, Yam...)',
                    'value' => [
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
                        'Jackie Chun' => 'H47.png',
                    ],
                ],
                'H3' => [
                    'label' => 'Autres personnages',
                    'value' => [
                        'Hercule' => 'H5.png',
                        'Tortue Génial' => 'H1.png',
                        'Plume' => 'H7.png',
                        'Yajirobé' => 'H9.png',
                        'Yajirobé (un peu plus gros)' => 'H20.png',
                        'Mr Popo' => 'H12.png',
                        'Maître Karine' => 'H11.png',
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
                        'Maître des Grues' => 'H55.png',
                    ],
                ],
            ],
            Race::HUMAN_SAIYAJIN => [
                'HS1' => [
                    'label' => 'Sangohan',
                    'value' => [
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
                        'Sangohan du futur' => 'HS7.png',
                    ],
                ],
                'HS2' => [
                    'label' => 'Trunks',
                    'value' => [
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
                        'Trunks GT Super Saïyen' => 'HS38.png',
                    ],
                ],
                'HS3' => [
                    'label' => 'Sangoten',
                    'value' => [
                        'Sangoten normal' => 'HS18.png',
                        'Sangoten Super Saïyen' => 'HS19.png',
                        'Sangoten GT normal' => 'HS40.png',
                        'Sangoten GT Super Saïyen' => 'HS41.png',
                    ],
                ],
                'HS4' => [
                    'label' => 'Gotrunks',
                    'value' => [
                        'Gotrunks normal' => 'HS13.png',
                        'Gotrunks Super Saïyen' => 'HS14.png',
                        'Gotrunks Super Saïyen 3' => 'HS15.png',
                        'Gotrunks Super Saïyen 3 + Eclairs' => 'HS35.png',
                    ],
                ],
                'HS5' => [
                    'label' => 'Pan',
                    'value' => [
                        'Pan' => 'H101.png',
                        'Pan Bébé' => 'HS47.png',
                    ],
                ],
                'HS6' => [
                    'label' => 'Justicier masqué',
                    'value' => [
                        'Justicier masqué' => 'HS45.png',
                    ],
                ],
                'HS7' => [
                    'label' => 'Bra',
                    'value' => [
                        'Bra' => 'HS46.png',
                    ],
                ],
            ],
            Race::NAMEKIAN => [
                'N1' => [
                    'label' => 'Piccolo',
                    'value' => [
                        'Piccolo enfant' => 'N10.png',
                        'Piccolo' => 'N.png',
                        'Dieuccolo' => 'N1.png',
                    ],
                ],
                'N2' => [
                    'label' => 'Tout-puissant',
                    'value' => [
                        'Tout-puissant' => 'N3.png',
                    ],
                ],
                'N3' => [
                    'label' => 'Dendé',
                    'value' => [
                        'Dendé jeune' => 'N2.png',
                        'Dendé adolescent' => 'N7.png',
                    ],
                ],
                'N4' => [
                    'label' => 'Simple Namek',
                    'value' => [
                        'Namek tunique rouge' => 'N4.png',
                        'Namek tunique bleu' => 'N5.png',
                        'Namek tunique verte' => 'N6.png',
                    ],
                ],
                'N5' => [
                    'label' => 'Slug',
                    'value' => [
                        'Slug avec casque' => 'N8.png',
                        'Slug sans casque' => 'N9.png',
                    ],
                ],
            ],
            RACE::SAIYAJIN => [
                'S1' => [
                    'label' => 'Sangoku',
                    'value' => [
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
                        'Sangoku GT Super Saïyen 3 + Eclairs' => 'S27.png',
                    ],
                ],
                'S2' => [
                    'label' => 'Végéta',
                    'value' => [
                        'Végéta normal' => 'S3.png',
                        'Végéta normal habits saïyen' => 'S12.png',
                        'Végéta Super Saïyen' => 'S4.png',
                        'Végéta Super Saïyen 2' => 'S24.png',
                        'Super Végéta' => 'S29.png',
                        'Végéta Super Saïyen 4' => 'S11.png',
                        'Majin Végéta' => 'S20.png',
                        'Végéta mort' => 'S31.png',
                        'Végéta mort Super saïyen' => 'S32.png',
                        'Prince Végéta' => 'S52.png',
                    ],
                ],
                'S3' => [
                    'label' => 'Fusion Goku et Végéta',
                    'value' => [
                        'Végéto' => 'S13.png',
                        'Végéto Super Saïyen' => 'S14.png',
                        'Gogéta Super Saïyen 4' => 'S15.png',
                        'Gogéta Super Saïyen 2' => 'S19.png',
                        'Gogéta Super Saïyen 2 + Eclairs' => 'S26.png',
                    ],
                ],
                'S4' => [
                    'label' => 'Broly',
                    'value' => [
                        'Broly' => 'S17.png',
                        'Broly Super Saiyen' => 'S21.png',
                        'Broly Super Saiyen 2' => 'S22.png',
                        'Broly Super Saïyen Légendaire' => 'S18.png',
                        'Broly Super Saïyen Légendaire Altern 1' => 'S42.png',
                        'Broly Super Saïyen Légendaire Altern 2' => 'S50.png',
                        'Broly Super Saïyen Légendaire Altern 3' => 'S53.png',
                    ],
                ],
                'S5' => [
                    'label' => 'Autres Saïyens',
                    'value' => [
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
                        'Roi Végéta' => 'S51.png',
                    ],
                ],
            ],
            Race::ALIEN => [
                'A1' => [
                    'label' => 'Freezer et sa Famille',
                    'value' => [
                        'Freezer normal' => 'A12.png',
                        'Freezer première transformation' => 'A13.png',
                        'Freezer deuxième transformation' => 'A14.png',
                        'Freezer ultime transformation' => 'A4.png',
                        'Freezer puissance maximale' => 'A30.png',
                        'Metal Freezer' => 'A3.png',
                    ],
                ],
                'A2' => [
                    'label' => 'Commando Ginue',
                    'value' => [
                        'Giniue' => 'A7.png',
                        'Guldo' => 'A24.png',
                        'Geece' => 'A10.png',
                        'Barta' => 'A9.png',
                        'Recoom' => 'A8.png',
                    ],
                ],
                'A3' => [
                    'label' => 'Serviteur de Freezer',
                    'value' => [
                        'Monstre bleu clair' => 'A.png',
                        'Monstre vert clair' => 'A5.png',
                        'Monstre vert foncé' => 'A1.png',
                        'Monstre marron' => 'A11.png',
                        'Monstre marron foncé' => 'A6.png',
                        'Zabon' => 'A25.png',
                        'Doria' => 'A45.png',
                        'Soldat agé' => 'A41.png',
                        'Sauza' => 'A42.png',
                        'Kiwi' => 'A44.png',
                    ],
                ],
                'A4' => [
                    'label' => 'Baby',
                    'value' => [
                        'Baby' => 'A38.png',
                        'Baby végéta' => 'A15.png',
                        'Baby végéta gorille' => 'A39.png',
                    ],
                ],
                'A5' => [
                    'label' => 'Les mercenaires de l\'espace',
                    'value' => [
                        'Bojack 1ère transformation' => 'A23.png',
                        'Bojack 2ème transformation' => 'A16.png',
                        'Zangya' => 'A22.png',
                        'Gokua' => 'A29.png',
                        'Bido' => 'A36.png',
                    ],
                ],
                'A6' => [
                    'label' => 'Cooler',
                    'value' => [
                        'Cooler' => 'A18.png',
                        'Super Cooler' => 'A17.png',
                        'Metal Cooler' => 'A19.png',
                    ],
                ],
                'A7' => [
                    'label' => 'Tapion et son frère',
                    'value' => [
                        'Tapion' => 'A20.png',
                        'Petit frère de Tapion' => 'A21.png',
                    ],
                ],
                'A8' => [
                    'label' => 'Garlic',
                    'value' => [
                        'Garlic Junior' => 'A26.png',
                    ],
                ],
                'A9' => [
                    'label' => 'Janemba',
                    'value' => [
                        'Janemba1' => 'A27.png',
                        'Janemba2' => 'A28.png',
                    ],
                ],
                'A10' => [
                    'label' => 'Autres personnages',
                    'value' => [
                        'Arbitre de l\\\'autre monde' => 'A31.png',
                        'Catapy' => 'A32.png',
                        'Zold' => 'A33.png',
                        'Tald' => 'A34.png',
                        'Vinegar' => 'A35.png',
                        'Torbie' => 'A37.png',
                        'Rild' => 'A40.png',
                        'Saibaimen' => 'A43.png',
                    ],
                ],
            ],
            Race::CYBORG => [
                'C1' => [
                    'label' => 'Les C',
                    'value' => [
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
                        'C-20 sans chapeau' => 'C9.png',
                    ],
                ],
                'C2' => [
                    'label' => 'Cell',
                    'value' => [
                        'Cell première transformation' => 'C15.png',
                        'Cell deuxième transformation' => 'C7.png',
                        'Perfect Cell' => 'C1.png',
                        'Reborn Cell' => 'C14.png',
                        'Mini cell' => 'C6.png',
                    ],
                ],
                'C3' => [
                    'label' => 'Autres cyborgs',
                    'value' => [
                        'Taopaïppaï' => 'C5.png',
                        'Gil' => 'C16.png',
                    ],
                ],
            ],
            Race::MAJIN => [
                'M1' => [
                    'label' => 'Boubou',
                    'value' => [
                        'choice.shape' => '',
                        'Boubou' => 'M.png',
                        'Bou Mal incarné' => 'M9.png',
                        'Majin Bou' => 'M8.png',
                        'Majin Bou + Gotrunks' => 'M12.png',
                        'Majin Bou + Piccolo' => 'M11',
                        'Majin Bou + San Gohan' => 'M10.png',
                        'Bou Ultime' => 'M1.png',
                        'Super Oub' => 'M13.png',
                    ],
                ],
                'M2' => [
                    'label' => 'Babidi',
                    'value' => [
                        'choice.shape' => '',
                        'Babidi' => 'M2.png',
                    ],
                ],
                'M3' => [
                    'label' => 'Serviteurs',
                    'value' => [
                        'choice.shape' => '',
                        'Buïbuï' => 'M3.png',
                        'Sporovich' => 'M7.png',
                        'Yamu' => 'M5.png',
                        'Simple guerrier' => 'M4.png',
                        'Yakon' => 'M6.png',
                    ],
                ],
            ],
            Race::DRAGON => [
                'D1' => [
                    'label' => 'Dragon de couleur',
                    'value' => [
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
                        'Dragon marron clair' => 'D11.png',
                    ],
                ],
                'D2' => [
                    'label' => 'Li Sheron',
                    'value' => [
                        'choice.shape' => '',
                        'Première transformation' => 'D12.png',
                        'Première transformation Altern' => 'D21.png',
                        'Deuxième transformation' => 'D13.png',
                    ],
                ],
                'D3' => [
                    'label' => 'Autres personnages',
                    'value' => [
                        'choice.character' => '',
                        'Nova Shenron' => 'D14.png',
                        'Nova Shenron Altern' => 'D20.png',
                        'Uu Shenron' => 'D15.png',
                        'Chii Shenron' => 'D16.png',
                        'Otamine' => 'D17.png',
                        'Haze Shenron' => 'D18.png',
                        '??? Shenron' => 'D19.png',
                    ],
                ],
            ],
        ];
    }

    /**
     * @Annotations\Get("/ranking", name="")
     * @Annotations\Get("/ranking/{what}", name="_what")
     */
    public function rankingAction(Request $request, $what = null)
    {
        $rankingList = [
            'battle-points' => [
                'label' => 'ranking.battlePoints',
                'field' => 'p.battlePoints',
            ],
            'masterkill' => [
                'label' => 'ranking.serialKillers',
                'field' => 'masterKill',
                'description' => 'ranking.description.serialKillers',
            ],
            'good-killer' => [
                'label' => 'ranking.assassins',
                'field' => 'p.nbKillGood',
                'description' => 'ranking.description.assassins',
            ],
            'bad-killer' => [
                'label' => 'ranking.crusaders',
                'field' => 'p.nbKillBad',
                'description' => 'ranking.description.crusaders',
            ],
            'bounty-hunter' => [
                'label' => 'ranking.bounty',
                'field' => 'p.nbWanted',
            ],
            'hq-batter' => [
                'label' => 'ranking.hq.hitters',
                'field' => 'p.nbHitHq',
                'description' => 'ranking.description.hq.hitters',
            ],
            'hq-damage' => [
                'label' => 'ranking.hq.terrors',
                'field' => 'p.nbDamageHq',
                'description' => 'ranking.description.hq.terrors',
            ],
            'hq-killer' => [
                'label' => 'ranking.hq.killers',
                'field' => 'p.nbKillHq',
                'description' => 'ranking.description.hq.killers',
            ],
            'npc_killer' => [
                'label' => 'ranking.hunters',
                'field' => 'p.nbKillNpc',
                'description' => 'ranking.description.hunters',
            ],
            'death' => [
                'label' => 'ranking.victims',
                'field' => 'p.deathCount',
                'description' => 'ranking.description.victims',
            ],
            'slap-given' => [
                'label' => 'ranking.slap.given',
                'field' => 'p.nbSlapGiven',
            ],
            'slap-taken' => [
                'label' => 'ranking.slap.taken',
                'field' => 'p.nbSlapTaken',
            ],
        ];

        if (empty($rankingList[$what])) {
            $what = 'battle-points';
        }

        $playerRepo = $this->repos()->getPlayerRepository();
        $page = $request->query->get('page', 1);
        $page = $page < 1 ? 1 : $page;

        $qb = $playerRepo->createQueryBuilder('p');
        $qb->addSelect('(p.nbKillGood + p.nbKillBad + p.nbKillNpc) AS masterKill')
            ->where(
                $qb->expr()->in(
                    'p.side',
                    [Side::GOOD, Side::BAD]
                )
            )
            ->andWhere('p.enabled = true');

        $type = $request->query->get('type');
        $player = $this->getUser();
        if (!empty($player) && !empty($type) && $type == 'guild') {
            $guildPlayer = $player->getGuildPlayer();
            if (!empty($guildPlayer) && $guildPlayer->isEnabled()) {
                $qb->innerJoin('p.guildPlayer', 'gp', 'WITH', 'gp.player = p')
                    ->andWhere('gp.guild = :guild')
                    ->setParameter('guild', $guildPlayer->getGuild());
            }
        }

        $who = $request->query->get('who');
        if (!empty($who) && ($playerSearched = $playerRepo->findOneByName($who))) {
            $orderField = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $rankingList[$what]['field']));
            if ($orderField == 'master_kill') {
                $orderField = '(p.nb_kill_good + p.nb_kill_bad + p.nb_kill_npc)';
            }
            $sqlRequest = <<<EOF
SELECT
 r.row_number
FROM (
  SELECT
    id,
    (p.nb_kill_good + p.nb_kill_bad + p.nb_kill_npc) AS master_kill,
    ROW_NUMBER() OVER(ORDER BY $orderField DESC) AS row_number
  FROM player AS p
  WHERE p.side_id IN (:sides) AND p.enabled = true
) AS r
WHERE r.id = :id
EOF;
            $rsm = new ResultSetMapping();
            $rsm->addScalarResult('row_number', 'row_number');
            $query = $this->em()->createNativeQuery($sqlRequest, $rsm);
            $query->setParameters([
                'id' => $playerSearched->getId(),
                'sides' => [Side::BAD, Side::GOOD],
            ]);
            $rowNumber = $query->getSingleScalarResult();
            $page = ceil($rowNumber / self::LIMIT_PER_PAGE);
        }

        $qb->setFirstResult(($page - 1) * self::LIMIT_PER_PAGE)
            ->setMaxResults(self::LIMIT_PER_PAGE)
            ->addOrderBy($rankingList[$what]['field'], 'DESC');
        $ranking = new Paginator($qb, false);

        $result = [];
        foreach ($ranking as $playerRank) {
            $result[] = $playerRank;
        }

        return [
            'count' => count($ranking),
            'current' => $page,
            'limit' => self::LIMIT_PER_PAGE,
            'what' => $what,
            'type' => $type,
            'who' => !empty($playerSearched) ? $playerSearched->getName() : null,
            'result' => [
                'ranking' => $result,
                'rankingList' => $rankingList,
            ],
        ];
    }
}
