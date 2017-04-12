<?php

namespace Dba\GameBundle\EventListener;

use DateTime;
use Dba\GameBundle\Event\DbaEvents;
use Dba\GameBundle\Event\ActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PlayerSpellSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            DbaEvents::AFTER_SPELL => 'afterSpellAttack',
        );
    }

    public function afterSpellAttack(ActionEvent $event)
    {
        $messages = $event->getData()['messages'];
        $damages = $event->getData()['damages'];
        $playerSpell = $event->getData()['playerSpell'];
        if (empty($damages) || $damages < 0) {
            $damages = 0;
            switch ($playerSpell->getSpell()->getId()) {
                case 2: // Kaioken
                    $messages[] = 'Tu concentres ton énerge et tu augmentes ta puissance grâce au sort Kaioken !';
                    break;

                case 9: // SSJ3 Saïyen
                    $messages[] = 'Tu viens de te transformer en Super Saiyen 3 !!! ';
                    break;

                case 26: // Morsure du Soleil
                    $messages[] = 'Tu viens d\'éblouir ton adversaire !!! ';
                    break;

                case 30: // Destruction de détecteur
                    $messages[] = 'Tu viens d\'handicaper ton adversaire qui ne pourra plus t\'analyser !!!';
                    break;

                case 42: // Souffle toxique des Dragons
                    $messages[] = 'Tu viens d\'empoisonner ton adversaire !!! ';
                    break;

                case 52:
                    $messages[] = 'Ta cible sera infatigable pendant 10 tours !';
                    break;

                case 57: // Régénération cellulaire
                    $messages[] = 'A présent, rien ni personne ne pourra te vaincre...';
                    break;

                case 69: // Copie des aliens
                    $messages[] = 'Tel Baby, toutes tes compétences sont augmentées par celles de ta cible.';
                    break;

                case 70: // Décomposition cellulaire
                    $messages[] = 'A présent, tu esquiveras les attaques magiques de tes adversaires.';
                    break;

                case 93: // SSJ2 Saiyens
                    $messages[] = 'Tu viens de te transformer en Super Sa&iuml;yen 2 !!';
                    break;

                case 85: // SSJ1 Saiyen
                    $messages[] = 'Tu viens de te transformer en Super Sa&iuml;yen !!';
                    break;

                case 83: // SSJ2 des HS
                    $messages[] = 'Tu viens de te transformer en Super Sa&iuml;yen 2 !!';
                    break;

                case 84: // SSJ3 des HS
                    $messages[] = 'Tu viens de te transformer en Super Sa&iuml;yen 3 !!';
                    break;

                case 86: // Porunga des Dragons
                    $messages[] = 'Tu viens de te transformer en Porunga, le Dieu des r&ecirc;ves  !!';
                    break;

                case 89: // Li Shenron des Dragons
                    $messages[] = 'Tu viens de te transformer en Li Shenron !!';
                    break;

                case 96: // Torture démoniaque
                    $messages[] = 'Tu lances de puissants rayons sur ta cible, handicapant très sa puissance';
                    break;

                case 75: // Libération pouvoir
                    $messages[] = 'Tu quintuples l\'esprit de ta cible.';
                    break;

                case 100: // Metal Freezer
                    $messages[] = 'Tu absorbes l\'énergie de ta cible et te transforme en Alien métalique !!';
                    break;

                case 101: // Metal Cooler
                    $messages[] = 'Mutation cybernétique effectuée avec succès !!';
                    break;

                case 102: // Super saïyen des humains saïyens
                    $messages[] = 'Tu viens de te transformer en Super Sa&iuml;yen !!';
                    break;

                case 103: // Action experte guerrier saiyen
                    $messages[] = 'Tu viens de te transformer en gorille géant !!';
                    break;

                case 104: // Action experte guerrier malduchi
                    $messages[] = 'Après avoir créé ton double négatif, ' .
                                'celui-ci t\'absorbe et te transforme en Majin Boo !!';
                    break;

                case 105: // Action experte guerrier humain
                    $messages[] = 'Après un entrainement dans la capsule 3G, tu te sens à présent surpuissant !!';
                    break;

                case 106: // Action experte guerrier humain saiyen
                    $messages[] = 'Tu viens de te transformer en gorille géant !!';
                    break;

                case 107: // Action experte guerrier namek
                    $messages[] = 'Ta masse musculaire a doublé faisant de toi un Super Namek !!';
                    break;

                case 108: // Action experte guerrier alien
                    $messages[] = 'Mutation génétique terminée !!';
                    break;

                case 109: // Action experte guerrier cyborg
                    $messages[] = 'Tu viens d\'évoluer en Neo-Cyborg !!';
                    break;

                case 110: // Action experte guerrier dragon
                    $messages[] = 'Mutation achevée. Te voilà devenu un dragon guerrier !!';
                    break;
                case 17:
                    $messages[] = 'masenko.dodge';
                    break;
                case 61:
                    $message[] = 'strangulation.dodge';
                    break;
                case 21:
                    $message[] = 'sokidan.dodge';
                    $damages = mt_rand(1, 20);
                    break;
            }
        } else {
            switch ($playerSpell->getSpell()->getId()) {
                case 72: // Sword Attack
                    $damages *= 1.5;
                    $messages[] = 'janemba.attack';
                    $message[] = "";
                    break;
                case 78: // Kamehameha Père Fils
                    $damages *= 2.5;
                    $message[] = "Tu te transforme en Super Saïyen 2 et l'âme de ton père renforce ton attaque";
                    break;
                case 79: // Tempête de l'enfer
                    $damages *= 2.5;
                    $message[] = "De véritables météorites viennent de s'écraser sur ton adversaire";
                    break;
            }
        }

        return ceil($damages);
    }
}
