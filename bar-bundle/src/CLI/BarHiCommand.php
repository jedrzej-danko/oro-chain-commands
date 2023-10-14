<?php

namespace OroChain\BarBundle\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BarHiCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('bar:hi');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hi from Bar!');
        return Command::SUCCESS;
    }
}