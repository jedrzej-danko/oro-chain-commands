<?php

namespace OroChain\ChainCommandBundle\EventListener;

use OroChain\ChainCommandBundle\ChainConfig;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
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

    public function __invoke(ConsoleTerminateEvent $event)
    {
        $commandName = $event->getCommand()->getName();
        $output = $event->getOutput();

        if ($event->getExitCode() !== Command::SUCCESS) {
            $output->writeln('Command failed, skipping chain', OutputInterface::VERBOSITY_DEBUG);
            return;
        }


        $chain = $this->config->getChainForCommand($commandName);
        if ($chain) {
            $application = $event->getCommand()->getApplication();
            $application->doRun()
            $output->writeln("Command $commandName has chain: ". join(',', $chain), OutputInterface::VERBOSITY_DEBUG);
        } else {
            $output->writeln("Command $commandName has no chain", OutputInterface::VERBOSITY_DEBUG);
        }
    }
}