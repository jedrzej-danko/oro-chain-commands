<?php

namespace OroChain\ChainCommandBundle\EventListener;

use OroChain\ChainCommandBundle\ChainConfig;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AfterCommandListener
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
     * Intercepting the master command's termination event and launching the chain
     *
     * @param ConsoleTerminateEvent $event
     * @return void
     * @throws ExceptionInterface
     */
    public function __invoke(ConsoleTerminateEvent $event): void
    {
        $commandName = $event->getCommand()->getName();

        $chain = $this->config->getChainForCommand($commandName);
        if (!$chain || !count($chain->chain)) {
            return;
        }

        $output = $event->getOutput();

        if ($event->getExitCode() !== Command::SUCCESS) {
            $this->logger->error("Master command $commandName failed, skipping chain");
            $output->writeln('<error>Master command failed, skipping chain</error>', OutputInterface::VERBOSITY_DEBUG);
            return;
        }

        $this->logger->info("Executing $commandName chain members:");

        $application = $event->getCommand()->getApplication();
        foreach ($chain->chain as $command) {
            $application->find($command)->run($event->getInput(), $output);
        }
        $this->logger->info("Execution of $commandName chain completed");

    }
}