<?php

namespace Dba\GameBundle\Command;

use Exception;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Entity\Player;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\LockableTrait;

class NpcPlayCommand extends BaseCommand
{
    use LockableTrait;

    const TAB = "\t";

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dba:npc:play')
            ->setDescription('Let Npcs play.')
            ->setDefinition(
                [
                    new InputArgument('number', InputArgument::REQUIRED, 'Number of npc')
                ]
            )
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command will let npcs play:

  <info>php %command.full_name% 500</info>
EOT
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

        $number = $input->getArgument('number');
        $playerService = $this->services()->getPlayerService();
        $playerRepo = $this->repos()->getPlayerRepository();
        $npcs = $playerRepo->findBy(
            ['side' => Side::NPC, 'enabled' => true],
            ['updatedAt' => 'ASC'],
            $number
        );

        $message = sprintf('Npcs found <comment>%s</comment>', count($npcs));
        $output->writeln($message);
        $this->getLogger()->info($message);
        foreach ($npcs as $npc) {
            $message = sprintf('Npc: <info>%s (%d)</info>', $npc->getName(), $npc->getId());
            $output->writeln($message);
            $this->getLogger()->info($message);
            $retry = 0;
            while (empty($players) && $npc->getMovementPoints() >= 6 && $retry < 3) {
                $players = $playerRepo->findNearestPlayers($npc);
                if (!empty($players)) {
                    continue;
                }

                list($result, $move) = $playerService->move(
                    $npc,
                    Player::AVAILABLE_MOVE[array_rand(Player::AVAILABLE_MOVE)]
                );
                if (empty($result)) {
                    $retry++;
                } else {
                    $npc->usePoints(Player::MOVEMENT_POINT, Player::MOVEMENT_ACTION + (int) ($move > 1));
                    $message = sprintf(
                        self::TAB . 'Moved in %d / %d',
                        $npc->getX(),
                        $npc->getY()
                    );
                    $output->writeln($message);
                    $this->getLogger()->info($message);

                    $this->em()->persist($npc);
                    $this->em()->flush();
                }
            }

            if (empty($players)) {
                continue;
            }

            $target = $players[array_rand($players)];
            $retry = 0;
            while (($target->getX() != $npc->getX() || $target->getY() != $target->getY()) &&
                   $npc->getMovementPoints() >= 6 && $retry < 3
            ) {
                $where = '';
                if ($npc->getY() > $target->getY()) {
                    $where = 'n';
                } elseif ($npc->getY() < $target->getY()) {
                    $where = 's';
                }

                if ($npc->getX() > $target->getX()) {
                    $where .= 'w';
                } elseif ($npc->getX() < $target->getX()) {
                    $where .= 'e';
                }

                list($result, $move) = $playerService->move($npc, $where);
                if (empty($result)) {
                    $retry++;
                } else {
                    $npc->usePoints(Player::MOVEMENT_POINT, Player::MOVEMENT_ACTION + (int) ($move > 1));
                    $message = sprintf(
                        self::TAB . 'Moved in direction of <info>%s</info>(%d) in %d / %d',
                        $target->getName(),
                        $target->getId(),
                        $npc->getX(),
                        $npc->getY()
                    );
                    $output->writeln($message);
                    $this->getLogger()->info($message);

                    $this->em()->persist($npc);
                    $this->em()->flush();
                }
            }

            while ($target->getX() == $npc->getX() && $target->getY() == $target->getY() &&
                   $npc->getActionPoints() >= Player::ATTACK_ACTION
            ) {
                list($luck, $damages, $isDead) = $playerService->attack($npc, $target);
                if (($luck >= -4 && $luck < -3) || $damages <= 0) {
                    $eventMessage = 'event.action.attack.dodge';
                    $message = sprintf(
                        self::TAB . 'Attack <info>%s</info> (%d) but he dodges.',
                        $target->getName(),
                        $target->getId()
                    );
                    $output->writeln($message);
                    $this->getLogger()->info($message);
                } else {
                    $eventMessage = 'event.action.attack.npc';
                    $message = sprintf(
                        self::TAB . 'Attack <info>%s</info> (%d) and he did %d damages.',
                        $target->getName(),
                        $target->getId(),
                        $damages
                    );
                    $output->writeln($message);
                    $this->getLogger()->info($message);
                }
                $playerService->addEvent(
                    $npc,
                    $target,
                    $eventMessage,
                    [
                        'damages' => $damages
                    ]
                );

                if ($isDead) {
                    $playerService->addEvent(
                        $npc,
                        $target,
                        'event.action.attack.killed'
                    );

                    $message = self::TAB . 'Enemy die';
                    $output->writeln($message);
                    $this->getLogger()->info($message);
                }

                $npc->usePoints(Player::ACTION_POINT, Player::ATTACK_ACTION);
                $this->em()->persist($npc);
                $this->em()->persist($target);
                $this->em()->flush();
            }
        }

        $this->release();
    }
}
