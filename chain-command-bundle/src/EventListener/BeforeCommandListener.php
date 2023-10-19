<?php

namespace OroChain\ChainCommandBundle\EventListener;

use OroChain\ChainCommandBundle\ChainConfig;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class BeforeCommandListener
{
    use LoggerAwareTrait;

    private ChainConfig $config;

    /**
     * @param ChainConfig $config
     */
    public function __construct(ChainConfig $config)
    {
        $this->config = $config;
    }


    public function __invoke(ConsoleCommandEvent $event) : void
    {
        $output = $event->getOutput();
        $commandName = $event->getCommand()->getName();

        $output->writeln("BeforeCommandListener invoked", OutputInterface::VERBOSITY_DEBUG);
        $output->writeln("Command: $commandName", OutputInterface::VERBOSITY_DEBUG);

        if ($this->config->isChainMember($commandName)) {
            $output->writeln("Error: " . $commandName . " command is a member of a command chain and cannot be executed on its own.", OutputInterface::VERBOSITY_QUIET);
            $event->disableCommand();
        }

        $chain = $this->config->getChainForCommand($commandName);
        if ($chain) {
            $output->writeln("Command $commandName has chain: ". join(',', $chain), OutputInterface::VERBOSITY_DEBUG);
        } else {
            $output->writeln("Command $commandName has no chain", OutputInterface::VERBOSITY_DEBUG);
        }


//        if ($this->isChainMember($commandName)) {
//            $output->writeln("Error: " . $commandName . " command is a member of a command chain and cannot be executed on its own.", OutputInterface::VERBOSITY_QUIET);
//            $event->disableCommand();
//        }
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