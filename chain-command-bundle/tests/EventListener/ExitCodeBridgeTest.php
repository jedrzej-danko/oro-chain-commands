<?php

namespace OroChain\ChainCommandBundle\EventListener;

use PHPUnit\Framework\TestCase;

class ExitCodeBridgeTest extends TestCase
{
    public function test_when_ExitCode_is_set_chain_execution_flag_is_set()
    {
        $exitCodeBridge = new ExitCodeBridge();
        $exitCodeBridge->setExitCode(123);

        self::assertTrue($exitCodeBridge->chainCommandWasExecuted());
        self::assertEquals(123, $exitCodeBridge->getExitCode());
    }

    public function test_reset_clears_chainCommandExecution_flag()
    {
        $exitCodeBridge = new ExitCodeBridge();
        $exitCodeBridge->setExitCode(123);

        // flag is set at this moment
        self::assertTrue($exitCodeBridge->chainCommandWasExecuted());

        $exitCodeBridge->reset();

        self::assertFalse($exitCodeBridge->chainCommandWasExecuted());
        self::assertEquals(0, $exitCodeBridge->getExitCode());
    }


}
