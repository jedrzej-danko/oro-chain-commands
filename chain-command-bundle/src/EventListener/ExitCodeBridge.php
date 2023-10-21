<?php

namespace OroChain\ChainCommandBundle\EventListener;

/**
 * Passes the exit code between BeforeCommandListener and AfterCommandListener
 *
 * This class is used to pass the exit code from the chained command to the AfterCommandListener
 * to set the correct exit code when operation is complete
 *
 * Default state is that the chain command was not executed, so the AfterCommandListener should ignore exit code value
 * Despite class name, it has nothing to do with the Bridge design pattern
 */
class ExitCodeBridge
{
    private bool $chainCommandWasExecuted = false;
    private int $exitCode = 0;

    /**
     * @return void
     */
    public function reset(): void
    {
        $this->chainCommandWasExecuted = false;
        $this->exitCode = 0;
    }

    public function setExitCode(int $exitCode): void
    {
        $this->chainCommandWasExecuted = true;
        $this->exitCode = $exitCode;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }

    public function chainCommandWasExecuted(): bool
    {
        return $this->chainCommandWasExecuted;
    }


}