<?php

namespace OroChain\ChainCommandBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * This class fixes the exit code of the command if the command chain was executed.
 *
 * Because BeforeCommandListener disables the default command execution,
 * the exit code is the ConsoleTerminateEvent is always 113.
 * However, the real exit code is stored in the ExitCodeBridge
 */
class AfterCommandListener
{
    private ExitCodeBridge $exitCodeBridge;

    /**
     * @param ExitCodeBridge $exitCodeBridge
     */
    public function __construct(ExitCodeBridge $exitCodeBridge)
    {
        $this->exitCodeBridge = $exitCodeBridge;
    }


    public function __invoke(ConsoleTerminateEvent $event): void
    {
        if ($this->exitCodeBridge->chainCommandWasExecuted()) {
            $event->setExitCode($this->exitCodeBridge->getExitCode());
        }
        $this->exitCodeBridge->reset();
    }
}