<?php

namespace App;

use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ChainMemberListener
{
    private ChainCommandConfig $config;

    /**
     * @param ChainCommandConfig $config
     */
    public function __construct(ChainCommandConfig $config)
    {
        $this->config = $config;
    }


    public function __invoke(ConsoleCommandEvent $event) : void
    {
        $output = $event->getOutput();
        $commandName = $event->getCommand()->getName();

        $output->writeln("ChainListener invoked", OutputInterface::VERBOSITY_DEBUG);
        $output->writeln("Command: $commandName", OutputInterface::VERBOSITY_DEBUG);


        if ($this->isChainMember($commandName)) {
            $output->writeln("Error: " . $commandName . " command is a member of a command chain and cannot be executed on its own.", OutputInterface::VERBOSITY_QUIET);
            $event->disableCommand();
        }
    }

    private function isChainMember($commandName): bool
    {
        foreach ($this->config->getChains() as $chainedCommands) {
            if (in_array($commandName, $chainedCommands)) {
                return true;
            }
        }
        return false;
    }
}