<?php

namespace App\Tests\Functional;

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\ApplicationTester;

class FooHelloTest extends KernelTestCase
{
    public function test_runs_fooHello_and_its_chain()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'foo:hello']);

        $tester->assertCommandIsSuccessful();
        $output = $tester->getDisplay();
        $this->assertStringContainsString('Hello from Foo!', $output);
        $this->assertStringContainsString('Hi from Bar!', $output);
    }

    public function test_prevents_executing_barHi()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $tester = new ApplicationTester($application);
        $tester->run(['command' => 'bar:hi']);
        self::assertEquals(113, $tester->getStatusCode());
    }

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}