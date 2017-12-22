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
use Symfony\Component\Console\Command\LockableTrait;

class SendMailCommand extends BaseCommand
{
    use LockableTrait;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('dba:mail:send')
            ->setDescription('Send mail in queue.')
            ->setHelp(<<<'EOT'
The <info>%command.name%</info> command let you send mail store in database:

  <info>php %command.full_name%</info>
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

        $mailService = $this->services()->getMailService();
        $mails = $this->repos()->getMailRepository()->findBy([
            'sentAt' => null,
        ]);

        $message = sprintf('Send a total of <comment>%d</comment> mails', count($mails));
        $output->writeln($message);
        $this->getLogger()->info($message);

        foreach ($mails as $mail) {
            $message = sprintf(
                'Send mail: <info>%s to %s</info>',
                $mail->getTemplateName(),
                $mail->getPlayer()->getEmail()
            );
            $output->writeln($message);

            try {
                $this->getLogger()->info($message);
                $mailService->send($mail);
            } catch (Exception $e) {
                $this->getLogger()->error($e);
            }
        }

        $this->release();
    }
}
