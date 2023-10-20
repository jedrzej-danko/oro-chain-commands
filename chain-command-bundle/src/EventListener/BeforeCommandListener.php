<?php

namespace OroChain\ChainCommandBundle\EventListener;

use OroChain\ChainCommandBundle\ChainConfig;
use OroChain\ChainCommandBundle\Console\LoggedConsoleOutput;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Exception\ExceptionInterface;

class BeforeCommandListener
{
    use LoggerAwareTrait;

    private ChainConfig $config;
    private LoggedConsoleOutput $output;

    /**
     * @param ChainConfig $config
     * @param LoggedConsoleOutput $output
     */
    public function __construct(ChainConfig $config, LoggedConsoleOutput $output)
    {
        $this->config = $config;
        $this->output = $output;
    }


    /**
     *
     * @param ConsoleCommandEvent $event
     * @return void
     * @throws ExceptionInterface
     */
    public function __invoke(ConsoleCommandEvent $event) : void
    {
        $output = $event->getOutput();
        $command = $event->getCommand();
        $commandName = $command->getName();

        if (null !== ($chain = $this->config->findChainContaining($commandName))) {
            $this->logger->error("Error: $commandName command is a member of {$chain->startsWith} command chain and cannot be executed on its own.");
            $output->writeln("<error>Error: $commandName command is a member of {$chain->startsWith} command chain and cannot be executed on its own.</error>");
            $event->disableCommand();
            return;
        }

        $chain = $this->config->getChainForCommand($commandName);
        if (!$chain) {
            return;
        }

        $event->disableCommand();

        $this->logger->info("$commandName is a master command of a command chain that has registered member commands");
        foreach ($chain->chain as $chainedCommand) {
            $this->logger->info("Command $chainedCommand is a member of $commandName command chain");
        }

        $this->logger->info("Executing $commandName command itself first");
        $result = $command->run($event->getInput(), $this->output);

        if ($result !== Command::SUCCESS) {
            $this->logger->error("Master command $commandName failed, skipping chain");
            $output->writeln("<error>Master command failed, skipping chain</error>");
            return;
        }

        $application = $command->getApplication();

        $this->logger->info("Executing $commandName chain members:");

        foreach ($chain->chain as $chainedCommand) {
            $result = $application->find($chainedCommand)->run($event->getInput(), $this->output);

            if ($result !== Command::SUCCESS) {
                $this->logger->error("Chain element $chainedCommand failed, breaking chain");
                $output->writeln("<error>Chain element $chainedCommand failed, breaking chain</error>");
                return;
            }
        }

        $this->logger->info("Execution of $commandName chain completed");
    }

}