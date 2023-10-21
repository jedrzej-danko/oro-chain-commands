<?php

namespace OroChain\ChainCommandBundle\EventListener;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AfterCommandListenerTest extends TestCase
{
    public function test_when_chain_was_excuted_exit_code_is_copied_from_ExitCodeBridge()
    {
        $exitCodeBridge = new ExitCodeBridge();
        $exitCodeBridge->setExitCode(123);

        $event = $this->createEventWithExitCode(113);

        $listener = new AfterCommandListener($exitCodeBridge);
        $listener($event);

        self::assertEquals(123, $event->getExitCode());
    }

    public function test_when_chain_was_not_executed_exit_code_is_not_changed()
    {
        $exitCodeBridge = new ExitCodeBridge();

        $event = $this->createEventWithExitCode(113);

        $listener = new AfterCommandListener($exitCodeBridge);
        $listener($event);

        // when the chain was not executed
        self::assertFalse($exitCodeBridge->chainCommandWasExecuted());
        // the exit code is not changed
        self::assertEquals(113, $event->getExitCode());
    }

    private function createEventWithExitCode(int $exitCode) : ConsoleTerminateEvent
    {
        $command = $this->createMock(Command::class);
        $input = $this->createMock(InputInterface::class);
        $output = $this->createMock(OutputInterface::class);
        return new ConsoleTerminateEvent($command, $input, $output, $exitCode);
    }



}
