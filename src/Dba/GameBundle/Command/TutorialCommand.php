<?php

namespace Dba\GameBundle\Command;

use DateTime;
use Dba\GameBundle\Entity\Map;
use Dba\GameBundle\Entity\MapObject;
use Dba\GameBundle\Entity\MapObjectType;
use Dba\GameBundle\Entity\Object;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\PlayerQuest;
use Dba\GameBundle\Entity\Quest;
use Dba\GameBundle\Entity\QuestNpc;
use Dba\GameBundle\Entity\Race;
use Dba\GameBundle\Entity\Side;
use Exception;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TutorialCommand extends BaseCommand
{
    use LockableTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dba:tutorial')
            ->setDescription('Reresh tutorial objects and npcs.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command refresh tutorial:

  <info>php %command.full_name%</info>
EOT
            );

        $this->addOption(
            'clean',
            null,
            InputOption::VALUE_OPTIONAL,
            'Should clean unused maps',
            false
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');
            return 0;
        }

        $clean = $input->getOption('clean');
        if (!empty($clean)) {
            $this->cleanMaps($input, $output);
        } else {
            $this->createOrUpdateTutorial($input, $output);
        }

        $this->em()->flush();
        $this->release();
    }

    protected function cleanMaps(InputInterface $input, OutputInterface $output)
    {
        $playerRepo = $this->repos()->getPlayerRepository();
        $playerQuestRepo = $this->repos()->getPlayerQuestRepository();
        $questRepo = $this->repos()->getQuestRepository();
        $maps = $this->repos()->getMapRepository()->findBy([
            'type' => Map::TYPE_TUTORIAL,
        ]);

        $message = sprintf('Clean maps <comment>%d</comment>', count($maps));
        $output->writeln($message);
        $this->getLogger()->info($message);
        $originalQuest = $this->repos()->getQuestRepository()->findOneByMap(
            $this->repos()->getMapRepository()->findOneById(Map::TUTORIAL)
        );

        foreach ($maps as $map) {
            $hasPlayer = false;
            $players = $playerRepo->findByMap($map);
            foreach ($players as $player) {
                if ($player->isPlayer()) {
                    $hasPlayer = true;
                    break;
                }
            }

            if ($hasPlayer) {
                $message = sprintf('Player found on map <info>%d</info>', $map->getId());
                $output->writeln(self::TAB . $message);
                $this->getLogger()->info($message);
            } else {
                $message = sprintf('Clear map: <info>%d</info>', $map->getId());
                $output->writeln(self::TAB . $message);
                $this->getLogger()->info($message);

                $playerQuest = $playerQuestRepo->findOneByQuest($questRepo->findOneByMap($map));
                $playerQuest->setQuest($originalQuest);
                $playerQuest->setStatus(PlayerQuest::STATUS_FINISHED);
                $this->em()->persist($playerQuest);

                foreach ($players as $player) {
                    $this->em()->remove($player);
                }
                $this->em()->remove($map);
            }
        }
    }

    protected function createOrUpdateTutorial(InputInterface $input, OutputInterface $output)
    {
        $tutorial = $this->repos()->getMapRepository()->findOneById(Map::TUTORIAL);
        $message = sprintf('Update map <comment>%s</comment>', $tutorial->getName());
        $output->writeln($message);
        $this->getLogger()->info($message);
        $objects = [
            [
                'type' => MapObjectType::BUSH,
                'x' => 9,
                'y' => 10,
                'object' => Object::DEFAULT_BERRIES,
                'number' => 4,
            ],
            [
                'type' => MapObjectType::BUSH,
                'x' => 6,
                'y' => 3,
                'object' => Object::DEFAULT_BERRIES,
                'number' => 3
            ],
            [
                'type' => MapObjectType::ZENI,
                'x' => 10,
                'y' => 4,
                'number' => 10,
            ],
            [
                'type' => MapObjectType::ZENI,
                'x' => 7,
                'y' => 10,
                'number' => 20,
            ],
            [
                'type' => MapObjectType::SIGN,
                'x' => 6,
                'y' => 10,
                'extra' => [
                    MapObject::EXTRA_DIALOGUE => 'Ne fonce pas tête baissée. Consulte tous les menus qui ' .
                    'sont à ta disposition pour bien comprendre les bases du jeu.',
                    MapObject::EXTRA_DIALOGUE => 'Prends ton temps, ici, personne ne pourra venir te déranger.',
                    MapObject::EXTRA_DIALOGUE => 'Shu.',
                ]
            ],
        ];

        foreach ($objects as $mapObject) {
            $type = $this->repos()->getMapObjectTypeRepository()->find($mapObject['type']);
            $newMapObject = $this->repos()->getMapObjectRepository()->findOneBy([
                'y' => $mapObject['y'],
                'x' => $mapObject['x'],
                'mapObjectType' => $type,
                'map' => $tutorial,
            ]);

            if (empty($newMapObject)) {
                $newMapObject = new MapObject();
            }

            $newMapObject->setMap($tutorial);
            $newMapObject->setX($mapObject['x']);
            $newMapObject->setY($mapObject['y']);
            if (!empty($mapObject['object'])) {
                $newMapObject->setObject(
                    $this->repos()->getObjectRepository()->find($mapObject['object'])
                );
            }

            if (!empty($mapObject['number'])) {
                $newMapObject->setNumber($mapObject['number']);
            }

            $newMapObject->setMapObjectType($type);
            $message = sprintf('Update: <info>%s</info>', $newMapObject->getMapObjectType()->getName());
            $output->writeln(self::TAB . $message);
            $this->getLogger()->info($message);
            $this->em()->persist($newMapObject);
        }


        $quest = $this->repos()->getQuestRepository()->findOneBy([
            'map' => $tutorial,
        ]);

        if (empty($quest)) {
            $quest = new Quest();
        }

        $quest->setImage('2.png');
        $quest->setName('Que l\'histoire commence');
        $quest->setNpcName('Shu');
        $quest->setEnabled(true);
        $history = <<<EOT
Bonjour à toi voyageur.
Mon maître <strong>Pilaf</strong> m'a envoyé auprès de toi pour répondre à tes questions et t'initier à ton nouveau lieu de résidence...

A l'Est d'ici se trouve une immense île que mon maître a créé grâce à des boules sacrées permettant d'exaucer des voeux... Et il y envoie des guerriers tout comme toi...

Je ne devrais pas te le dire mais comme je ne veux pas que mon maître se mette en colère qu'il n'y a pas d'animation... et bien je dois t'avouer qu'il fait ça pour se divertir, vous regarder vous battre...

Quoi qu'il en soit l'île où tout se passera n'est pas celle où nous nous trouvons. Celle-ci ne sert que de passage, portail, avant que tu sois envoyé là-bas... Bon je suppose que ça suffira comme explication... Au pire tu as dans ta poche un petit livre appelé « Histoire» qui te renseignera mieux...


Maintenant je vais t'apprendre le minimum pour survivre, tu apprendras le reste tout seul comme un grand... Pour commencer regardons la barre de menu à gauche.

- <strong>Position :</strong> Il s'agit là de ta position sur l'île ou le lieu où tu es... Rien de compliqué.

- <strong>Zénis :</strong> C'est la monnaie qui est utilisée. Le nombre représente ta fortune. Même si une partie peut être stockée en banque.

- <strong>Vie :</strong> Comme le nom l'indique, ta vie restante et maximum.

- <strong>Ki :</strong> Il s'agit de tes points de magie. Ce sera utile pour les mages essentiellement.

- <strong>PA :</strong> Points d'actions. Ils servent à effectuer tout un tas d'action que tu découvriras. Tu pourras grâce à eux frapper, te soigner, ramasser des choses, etc. Je ne vais pas te gâcher le plaisir non plus en te disant tout... Tout du moins tu ne peux avoir que 100 PA au maximum. Ils se rechargent d'eux même régulièrement de 1 en 1. Le temps de rechargement est annoncé en dessous.

- <strong>Pmvts: </strong> Points de mouvements. Pour te déplacer il te faut utiliser ces points là. Ils fonctionnent de la même manière que les PA. Tu en as 150 au maximum. Tu peux échanger des PA contre des points de mouvements. Pour cela , un bouton s'affichera clairement. Libre à toi de faire la conversion ou non.

- <strong>Fatigue :</strong> Selon les événements que tu vas subir, tu vas gagner de la fatigue. Si tu en as trop tu seras moins efficace. Prends une potion contre la fatigue dans ce cas là.


Bon, maintenant que je t'ai tout expliqué. Passons à la pratique...

Regarde l'autre rive, là bas se dresse un loup et il bloque l'accès à divers buissons où j'ai l'habitude de récupérer des baies, puis il a l'air féroce. Je n'ose pas l'approcher. En regardant la rosace sous ton menu, tu y apperçevras une flèche directionnelle et pour un nombre de PMvts défini, tu pourras te déplacer en fonction de la direction choisie.

Ecoute, si tu es d'accord, je te téléporte sur la petite île, et si tu arrives à tuer ce loup, alors je te ferai débarquer sur l'île, d'accord ?
EOT;
        $quest->setHistory($history);
        $quest->setMap($tutorial);
        $quest->setX(3);
        $quest->setY(10);
        $quest->setOnAccepted([
            'message' => 'Tu as du courage dis moi, marché conclu ! ' .
            'Je viens de te téléporter sur l\'autre rive. Tu pourras y affronter le loup.',
            'map' => Map::TUTORIAL,
            'x' => 6,
            'y' => 10,
        ]);

        $message = <<<EOF
Shu : Génial, je vais pouvoir accéder à ma réserve !!!
T'es pas si faible que ça finallement. Pour te récompenser, je vais te téléporter sur l'île !
Il me reste plus qu'à te souhaiter bonne route et n'oublie pas que maintenant, tes points de mouvements ne sont plus illimités...
EOF;
        $quest->setOnCompleted([
            'message' => $message,
            'map' => Map::ISLAND,
        ]);
        if (count($quest->getNpcsNeeded()) === 0) {
            $npcNeeded = new QuestNpc();
        } else {
            $npcNeeded = $quest->getNpcsNeeded()->first();
        }

        $npcNeeded->setQuest($quest);
        $npcNeeded->setRace($this->repos()->getRaceRepository()->findOneByid(Race::PREDATOR));
        $npcNeeded->setNumber(1);
        $quest->addNpcsNeeded($npcNeeded);

        $message = sprintf('Update quest: <info>%s</info>', $quest->getName());
        $output->writeln(self::TAB . $message);
        $this->getLogger()->info($message);
        $this->em()->persist($quest);

        /**
         * Update npc
         */
        $npc = $this->repos()->getPlayerRepository()->findOneBy([
            'map' => $tutorial,
            'side' => $this->repos()->getSideRepository()->findOneById(Side::NPC),
        ]);

        if (empty($npc)) {
            $npc = new Player();
        }

        $name = 'Loup Féroce ' . mt_rand(1, 100000);
        $npc->setImage('3.png');
        $npc->setPassword(
            $this->getContainer()->get('security.password_encoder')->encodePassword(
                $npc,
                mt_rand()
            )
        );
        $npc->setZeni(50);
        $npc->setEnabled(true);
        $npc->setLevel(1);
        $npc->setName($name);
        $npc->setUsername($name);
        $npc->setEmail($name . '@dba.com');
        $npc->setRace($this->repos()->getRaceRepository()->findOneById(Race::PREDATOR));
        $npc->setRank(
            $this->repos()->getRankRepository()->findOneBy([
                'race' => $npc->getRace(),
                'level' => 1,
            ])
        );
        $npc->setActionPoints(60);
        $npc->setMovementPoints(100);
        $npc->setFatiguePoints(0);
        $npc->setBattlePoints(0);

        $npc->setSide($this->repos()->getSideRepository()->findOneById(Side::NPC));
        $npc->setSidePoints(0);

        $npc->setIp('127.0.0.1');
        $dateTime = new DateTime();
        $npc->setCreatedAt($dateTime);
        $npc->setUpdatedAt($dateTime);
        $npc->setActionUpdatedAt($dateTime);
        $npc->setKiUpdatedAt($dateTime);
        $npc->setMovementUpdatedAt($dateTime);
        $npc->setFatigueUpdatedAt($dateTime);
        $npc->setMap($tutorial);
        $npc->setX(3);
        $npc->setY(7);

        $npc->setMaxHealth(100);
        $npc->setKi(1);
        $npc->setMaxKi(1);
        $npc->setStrength(1);
        $npc->setResistance(1);
        $npc->setAccuracy(1);
        $npc->setAgility(1);
        $npc->setVision(1);
        $npc->setAnalysis(1);
        $npc->setSkill(1);
        $npc->setIntellect(1);

        $npc->setHealth($npc->getMaxHealth());

        $message = sprintf('Update npc: <info>%s</info>', $npc->getName());
        $output->writeln(self::TAB . $message);
        $this->getLogger()->info($message);

        $this->em()->persist($npc);
    }
}
