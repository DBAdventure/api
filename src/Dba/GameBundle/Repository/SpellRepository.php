<?php

namespace Dba\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Dba\GameBundle\Entity\Spell;
use Dba\GameBundle\Entity\Player;

class SpellRepository extends EntityRepository
{
    public function findIncompatible(Spell $spell, Player $target)
    {
        $ids = [$spell->getId()];
        // Recherche des sorts incompatibles :
        switch ($spell->getId()) {
            case 2: // Kaioken Saiyen compatible avec les champs de force bouclier etc
                array_push($ids, 9, 85, 93);
                break;
            case 9: // Super saiyen 3
                array_push($ids, 66, 52, 53, 43, 25, 27, 2);
                break;
            case 25: // champs de force mineur (humain)
                array_push($ids, 66, 69, 70, 100, 101, 43, 86, 89, 102, 83, 62, 63, 35, 97, 9, 85, 93, 84);
                break;
            case 27: // champs de force majeur
                array_push($ids, 66, 69, 70, 100, 101, 43, 86, 89, 102, 83, 62, 63, 94, 35, 97, 9, 85, 93, 84);
                break;
            case 53: // champs de force mineur (cyborg)
                array_push($ids, 66, 69, 100, 101, 102, 83, 62, 63, 94, 35, 43, 66, 70, 86, 89, 97, 9, 85, 93, 84);
                break;
            case 62: // ensorcellement Badibi
                array_push($ids, 25, 27, 35, 53, 43, 52, 66, 70);
                break;
            case 63: // Henkai Beam
                array_push($ids, 66, 52, 53, 43, 25, 27);
                break;
            case 66: // Garde du corps de Freezer
                array_push($ids, 9, 25, 27, 35, 53, 43, 62, 63, 75, 83, 84, 85, 86, 89, 94, 97, 100, 102);
                break;
            case 69: // COPIE
                array_push($ids, 66, 70, 52, 53, 43, 25, 27, 35, 75, 97);
                break;
            case 70: // Décomposition cellulaire
                array_push($ids, 69, 100, 101, 43, 62);
                break;
            case 94: // Absorption Suprême
                array_push($ids, 66, 25, 27, 53, 43, 52, 83, 84, 85);
                break;
            case 35: // Sacrifice Petit Coeur
                array_push($ids, 66);
                break;
            case 43: // protection de Shenron
                array_push($ids, 66, 69, 70, 100, 101, 102, 83, 57, 63, 94, 35, 9, 25, 27, 53, 83, 84, 85, 62, 89, 93);
                break;

            case 52: // endurance
                array_push($ids, 62, 69, 100, 101, 102, 63, 94, 83, 84, 85, 86, 89, 93);
                break;
            case 57: // Régénération cellulaire Malduchi
                array_push($ids, 43);
                break;
            case 93: //Super saiyen 2 Saiyen
                array_push($ids, 66, 2, 25, 27, 53, 52, 43, 2);
                break;
            case 83: // Super Saiyen 2 Colère Gohan
                array_push($ids, 66, 84, 25, 27, 53, 43, 52);
                break;
            case 84: // Super Saiyen 3 Humain Saiyen
                array_push($ids, 66, 84, 25, 27, 53, 43, 52);
                break;
            case 85: // Super Saiyen (fureur sur namek)
                array_push($ids, 66, 52, 2, 25, 27, 53, 43, 2);
                break;
            case 86: // Dieux des rêves
                array_push($ids, 66, 2, 52, 25, 27, 53, 89);
                break;
            case 89: // Li Shenron + DB
                array_push($ids, 66, 52, 86, 25, 27, 53);
                break;
            case 97: // Sacrifice Nail
                array_push($ids, 66, 52, 53, 43, 25, 27);
                break;
            case 100: // Métal Freezer
                array_push($ids, 66, 70, 52, 53, 43, 25, 27);
                break;
            case 101: // Métal Cooler
                array_push($ids, 66, 70, 52, 53, 43, 25, 27);
                break;
            case 102: // SSJ1 Humains saiyens
                array_push($ids, 66, 52, 53, 43, 25, 27);
                break;
        }

        $em = $this->getEntityManager();
        $playerSpells = $em->getRepository('DbaGameBundle:PlayerSpell')
            ->findBy(
                [
                    'spell' => $ids,
                    'target' => $target
                ]
            );
        return $playerSpells;
    }
}
