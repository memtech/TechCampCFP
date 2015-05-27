<?php

namespace OpenCFP\Console\Command;

use Cartalyst\Sentry\Sentry;
use Cartalyst\Sentry\Users\UserNotFoundException;
use OpenCFP\Console\BaseCommand;
use OpenCFP\Environment;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AdminPromoteCommand extends BaseCommand
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('admin:promote')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'Email address of user to promote to admin'),
                new InputOption('env', '', InputOption::VALUE_REQUIRED, 'The environment the command should run in')
            ])
            ->setDescription('Promote an existing (or new) user to be an admin')
            ->setHelp(<<<EOF
The <info>%command.name%</info> command promotes a user to the admin group for a given environment:

<info>php %command.full_name% speaker@opencfp.org --env=production</info>
<info>php %command.full_name% speaker@opencfp.org --env=development</info>
EOF
);
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Sentry $sentry */
        $sentry = $this->app['sentry'];
        $email = $input->getArgument('email');

        $output->writeln(sprintf('Retrieving account from <info>%s</info>...', $email));

        try {
            $user = $sentry->getUserProvider()->findByLogin($email);
            $output->writeln('  Found account...');

            if ($user->hasAccess('admin')) {
                $output->writeln(sprintf('The account <info>%s</info> already has Admin access', $email));
                exit(1);
            }

            $adminGroup = $sentry->getGroupProvider()->findByName('Admin');
            $user->addGroup($adminGroup);
            $output->writeln(sprintf('  Added <info>%s</info> to the Admin group', $email));
        } catch (UserNotFoundException $e) {
            $output->writeln(sprintf('<error>Error:</error> Could not find user by %s', $email));
            exit(1);
        }

        $output->writeln('Done!');
        exit(0);
    }
}
