<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

//#[AsEventListener]
class ChainCommandLauncher
{
    private ChainCommandConfig $config;

    /**
     * @param ChainCommandConfig $config
     */
    public function __construct(ChainCommandConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(ConsoleTerminateEvent $event): void
    {
        $commandName = $event->getCommand()->getName();
        $output = $event->getOutput();

        if ($event->getExitCode() !== Command::SUCCESS) {
            $output->writeln('Command failed, skipping chain', OutputInterface::VERBOSITY_DEBUG);
            return;
        }

        if ($this->isChainLauncher($commandName)) {
            $this->launchChain($commandName, $event);
        }
    }

    private function isChainLauncher($commandName): bool
    {
        return array_key_exists($commandName, $this->config->getChains());
    }

    /**
     * @throws ExceptionInterface
     */
    private function launchChain(string $commandName, ConsoleTerminateEvent $event): void
    {
        $chainedCommands = $this->config->getChains()[$commandName];
        $command = $event->getCommand();
        $output = $event->getOutput();
        $application = $command->getApplication();

        foreach ($chainedCommands as $chainedCommand) {
            try {
                $nextCommand = $application->find($chainedCommand);
                $nextCommand->run($event->getInput(), $output);
            } catch (CommandNotFoundException $e) {
                $output->writeln("Failed to run $chainedCommand chain $commandName: " . $e->getMessage());
            }
        }
    }
}