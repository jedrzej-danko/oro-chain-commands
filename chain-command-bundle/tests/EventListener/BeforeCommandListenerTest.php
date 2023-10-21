<?php

namespace OroChain\ChainCommandBundle\EventListener;

use OroChain\ChainCommandBundle\ChainConfig;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @covers \OroChain\ChainCommandBundle\EventListener\BeforeCommandListener
 */
class BeforeCommandListenerTest extends TestCase
{
    private string $firstMemberCommandName = 'test:member';
    private Command $firstMemberCommand;
    private string $secondMemberCommandName = 'test:member2';
    private Command $secondMemberCommand;

    private string $masterCommand = 'test:master';
    private string $otherCommand = 'test:other';
    private Application $application;

    private ExitCodeBridge $exitCodeBridge;

    protected function setUp(): void
    {
        $this->firstMemberCommand = $this->createMock(Command::class);
        $this->firstMemberCommand->method('getName')->willReturn($this->firstMemberCommandName);
        $this->secondMemberCommand = $this->createMock(Command::class);
        $this->secondMemberCommand->method('getName')->willReturn($this->secondMemberCommandName);

        $this->application = $this->createMock(Application::class);
        $this->application->method('find')
            ->willReturnMap([
                [$this->firstMemberCommandName, $this->firstMemberCommand],
                [$this->secondMemberCommandName, $this->secondMemberCommand],
            ]);
        $this->exitCodeBridge = new ExitCodeBridge();
    }

    public function test_it_preserves_chain_member_execution()
    {
        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($this->firstMemberCommandName);

        $config = $this->createMock(ChainConfig::class);

        // when the command is a member of a chain
        $config->expects($this->once())
            ->method('findChainContaining')
            ->with($this->firstMemberCommandName)
            ->willReturn($this->masterCommand);

        // I expect that the command is never run
        $command->expects($this->never())->method('run');

        $event = $this->eventFactory($command);

        $listener = $this->buildListener($config);
        $listener($event);

        // and the command is disabled
        self::assertFalse($event->commandShouldRun());
    }

    public function test_it_ignores_commands_that_are_not_chain_members_or_chain_masters()
    {
        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($this->otherCommand);

        $config = $this->createMock(ChainConfig::class);
        // when the command is not a member of a chain
        $config->method('findChainContaining')->willReturn(null);
        // nor it is the master of the chain
        $config->method('getChainForCommand')->willReturn(null);

        $listener = $this->buildListener($config);

        // I expect that the command is not run in this listener
        $command->expects($this->never())->method('run');
        // and the command is not disabled
        $event = $this->eventFactory($command);

        $listener($event);

        self::assertTrue($event->commandShouldRun());
        // and the ExitCodeBridge is not updated
        self::assertFalse($this->exitCodeBridge->chainCommandWasExecuted());
    }

    public function test_it_runs_chain_master_command_and_the_chain_members()
    {
        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($this->masterCommand);
        $command->method('getApplication')->willReturn($this->application);

        $config = $this->createMock(ChainConfig::class);
        // when the command is a master of a chain and has members
        $config->method('getChainForCommand')
            ->willReturn([$this->firstMemberCommandName, $this->secondMemberCommandName]);

        // I expect that the command is run in this listener
        $command->expects($this->once())->method('run')->willReturn(Command::SUCCESS);
        // and the firstCommand is run in this listener
        $this->firstMemberCommand->expects($this->once())->method('run')->willReturn(Command::SUCCESS);
        // and the secondCommand is run in this listener
        $this->secondMemberCommand->expects($this->once())->method('run')->willReturn(Command::SUCCESS);;

        $event = $this->eventFactory($command);

        $listener = $this->buildListener($config);
        $listener($event);

        // Finally, I expect that the master command is not executed elsewhere
        self::assertFalse($event->commandShouldRun());
        // and the ExitCodeBridge is updated with the master command's exit code
        self::assertTrue($this->exitCodeBridge->chainCommandWasExecuted());
        self::assertEquals(Command::SUCCESS, $this->exitCodeBridge->getExitCode());
    }

    public function test_if_master_command_fails_chain_is_not_executed()
    {
        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($this->masterCommand);
        $command->method('getApplication')->willReturn($this->application);

        $config = $this->createMock(ChainConfig::class);
        // when the command is a master of a chain and has members
        $config->method('getChainForCommand')
            ->willReturn([$this->firstMemberCommandName, $this->secondMemberCommandName]);
        // and the master command returns non-success return code
        $command->method('run')->willReturn(Command::FAILURE);
        // I expect that firstCommand is not run in this listener
        $this->firstMemberCommand->expects($this->never())->method('run');
        // and the secondCommand is not run in this listener
        $this->secondMemberCommand->expects($this->never())->method('run');

        $event = $this->eventFactory($command);

        $listener = $this->buildListener($config);
        $listener($event);
        // finally, I expect that ExitCodeBridge is updated with the master command's exit code
        self::assertTrue($this->exitCodeBridge->chainCommandWasExecuted());
        self::assertEquals(Command::FAILURE, $this->exitCodeBridge->getExitCode());
    }

    public function test_if_chain_member_fails_next_member_is_not_executed()
    {
        $command = $this->createMock(Command::class);
        $command->method('getName')->willReturn($this->masterCommand);
        $command->method('getApplication')->willReturn($this->application);

        $config = $this->createMock(ChainConfig::class);
        // when the command is a master of a chain and has members
        $config->method('getChainForCommand')
            ->willReturn([$this->firstMemberCommandName, $this->secondMemberCommandName]);
        // and the master command completes successfully
        $command->method('run')->willReturn(Command::SUCCESS);
        // but the firstCommand returns non-success return code
        $this->firstMemberCommand->expects($this->once())->method('run')->willReturn(Command::FAILURE);
        // I expect that the secondCommand is not run in this listener
        $this->secondMemberCommand->expects($this->never())->method('run');

        $event = $this->eventFactory($command);

        $listener = $this->buildListener($config);
        $listener($event);

        // finally, I expect that ExitCodeBridge is updated with the member command's exit code
        self::assertTrue($this->exitCodeBridge->chainCommandWasExecuted());
        self::assertEquals(Command::FAILURE, $this->exitCodeBridge->getExitCode());
    }

    private function eventFactory(Command $command, ?OutputInterface $output = null, ?InputInterface $input = null) : ConsoleCommandEvent
    {
        $output = $output ?? $this->createMock(OutputInterface::class);
        $input = $input ?? $this->createMock(InputInterface::class);
        return new ConsoleCommandEvent($command, $input, $output);
    }

    private function buildListener(ChainConfig $config, ?LoggerInterface $logger = null) : BeforeCommandListener
    {
        $listener = new BeforeCommandListener(
            $config,
           $this->exitCodeBridge
        );
        $listener->setLogger($logger ?? $this->createMock(LoggerInterface::class));
        return $listener;
    }



}
