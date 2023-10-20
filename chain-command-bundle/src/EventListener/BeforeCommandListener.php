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


    /**
     * Just before command execution check if it is a member of a command chain
     *
     * @param ConsoleCommandEvent $event
     * @return void
     */
    public function __invoke(ConsoleCommandEvent $event) : void
    {
        $output = $event->getOutput();
        $commandName = $event->getCommand()->getName();

        if (null !== ($chain = $this->config->findChainContaining($commandName))) {
            $this->logger->error("Error: $commandName command is a member of {$chain->startsWith} command chain and cannot be executed on its own.");
            $output->writeln("<error>Error: $commandName command is a member of {$chain->startsWith} command chain and cannot be executed on its own.</error>");
            $event->disableCommand();
        }

        $chain = $this->config->getChainForCommand($commandName);
        if (!$chain) {
            return;
        }

        $this->logger->info("$commandName is a master command of a command chain that has registered member commands");
        foreach ($chain->chain as $command) {
            $this->logger->info("Command $command is a member of $commandName command chain");
        }
        $this->logger->info("Executing $commandName command itself first");
    }

}