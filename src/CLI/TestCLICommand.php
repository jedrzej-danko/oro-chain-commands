<?php

namespace App\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCLICommand extends Command
{

    protected function configure() : void
    {
        $this
            ->setName('app:test-cli')
            ->setDescription('Test CLI Command');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln('Hello World!');
        return Command::SUCCESS;
    }

}