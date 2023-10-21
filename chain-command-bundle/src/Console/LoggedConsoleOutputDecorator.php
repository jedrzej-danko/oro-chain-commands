<?php

namespace OroChain\ChainCommandBundle\Console;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Decorates doWrite() method of OutputInterface to log all messages
 */
class LoggedConsoleOutputDecorator implements OutputInterface
{

    private OutputInterface $decoratedOutput;
    private LoggerInterface $logger;

    public function __construct(OutputInterface $decoratedOutput, LoggerInterface $logger)
    {
        $this->decoratedOutput = $decoratedOutput;
        $this->logger = $logger;
    }

    public function doWrite($message, $newline): void
    {
        $this->logger->info($message);
        $this->decoratedOutput->doWrite($message, $newline);
    }

    public function write(iterable|string $messages, bool $newline = false, int $options = 0)
    {
        $this->decoratedOutput->write($messages, $newline, $options);
    }

    public function writeln(iterable|string $messages, int $options = 0)
    {
        $this->decoratedOutput->writeln($messages, $options);
    }

    public function setVerbosity(int $level)
    {
        $this->decoratedOutput->setVerbosity($level);
    }

    public function getVerbosity(): int
    {
        return $this->decoratedOutput->getVerbosity();
    }

    public function isQuiet(): bool
    {
        return $this->decoratedOutput->isQuiet();
    }

    public function isVerbose(): bool
    {
        return $this->decoratedOutput->isVerbose();
    }

    public function isVeryVerbose(): bool
    {
        return $this->decoratedOutput->isVeryVerbose();
    }

    public function isDebug(): bool
    {
        return $this->decoratedOutput->isDebug();
    }

    public function setDecorated(bool $decorated)
    {
        $this->decoratedOutput->setDecorated($decorated);
    }

    public function isDecorated(): bool
    {
        return $this->decoratedOutput->isDecorated();
    }


    public function setFormatter(OutputFormatterInterface $formatter)
    {
        $this->decoratedOutput->setFormatter($formatter);
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return $this->decoratedOutput->getFormatter();
    }


}