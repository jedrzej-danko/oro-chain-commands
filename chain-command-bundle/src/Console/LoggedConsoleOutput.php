<?php

namespace OroChain\ChainCommandBundle\Console;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class LoggedConsoleOutput extends ConsoleOutput
{
    private LoggerInterface $logger;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function doWrite($message, $newline): void
    {
        $this->logger->info($message);
        parent::doWrite($message, $newline);
    }
}