<?php

namespace Dba\GameBundle\Command;

use Exception;
use Dba\GameBundle\Entity\Side;
use Dba\GameBundle\Entity\Player;
use Dba\GameBundle\Entity\EventType;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Command\LockableTrait;

class BankInterrestCommand extends BaseCommand
{
    use LockableTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dba:bank')
            ->setDescription('Calculate bank interrest.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command let a group of npc play:

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

        $bankRepo = $this->repos()->getBankRepository();
        $banks = $bankRepo->findAll();
        $playerService = $this->services()->getPlayerService();
        $message = sprintf('Calcul interrests of <comment>%d</comment> players', count($banks));
        $output->writeln($message);
        $this->getLogger()->info($message);
        foreach ($banks as $bank) {
            $target = $bank->getPlayer();
            if (!$target->isEnabled()) {
                continue;
            }

            $before = $bank->getZeni();
            $updatedValue = floor(($before * 10) / 100);
            $bank->setZeni($updatedValue + $before);

            $message = sprintf(
                'Bank: <info>%s (%d)</info>, before: <comment>%d</comment>, after: <comment>%d</comment>',
                $target->getName(),
                $target->getId(),
                $before,
                $bank->getZeni()
            );
            $output->writeln($message);

            try {
                $this->getLogger()->info($message);
                $this->em()->persist($bank);
                $this->em()->flush();

                $playerService->addEvent(
                    new Player(),
                    $target,
                    'game.bank.interest',
                    ['%goldBar%' => $updatedValue],
                    EventType::BANK
                );
            } catch (Exception $e) {
                $this->getLogger()->error($e);
            }
        }

        $this->release();
    }
}
