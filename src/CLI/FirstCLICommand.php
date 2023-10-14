<?php

namespace App\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FirstCLICommand extends Command
{
    protected function configure() : void
    {
        $this->setName('app:first-cli')
            ->setDescription('First CLI Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln('First command!');
        return Command::SUCCESS;
    }
}