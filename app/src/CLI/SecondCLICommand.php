<?php

namespace App\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SecondCLICommand extends Command
{
    protected function configure() : void
    {
        $this->setName('app:second-cli')
            ->setDescription('Second CLI Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln('Second command!');
        return Command::SUCCESS;
    }
}