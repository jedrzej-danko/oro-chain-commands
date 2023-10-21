<?php

namespace OroChain\ChainCommandBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ChainConfigTest extends TestCase
{
    private array $configArray = [
        'foo:hello' =>  ['members' => ['foo:hola', 'foo:bonjour']],
        'bar:hi' => ['members' => ['bar:hello', 'bar:bonjour']],
    ];
    private ParameterBagInterface $parameterBag;

    protected function setUp(): void
    {
        $this->parameterBag = $this->createMock(ParameterBagInterface::class);
        $this->parameterBag->method('get')
            ->willReturnCallback(function ($key) {
                if ($key === 'chain_command_bundle.chains') {
                    return $this->configArray;
                }
                return null;
            });
    }

    public function test_it_returns_command_chain_for_chain_master()
    {
        $config = new ChainConfig($this->parameterBag);

        $chain = $config->getChainForCommand('foo:hello');
        self::assertEquals(['foo:hola', 'foo:bonjour'], $chain);

        $chain = $config->getChainForCommand('bar:hi');
        self::assertEquals(['bar:hello', 'bar:bonjour'], $chain);

        $chain = $config->getChainForCommand('foo:hola');
        self::assertNull($chain);
    }

    public function test_it_returns_master_command_for_chain_member()
    {
        $config = new ChainConfig($this->parameterBag);

        $master = $config->findChainContaining('foo:hola');
        self::assertEquals('foo:hello', $master);
        $master = $config->findChainContaining('foo:bonjour');
        self::assertEquals('foo:hello', $master);

        $master = $config->findChainContaining('bar:hello');
        self::assertEquals('bar:hi', $master);
        $master = $config->findChainContaining('bar:bonjour');
        self::assertEquals('bar:hi', $master);

        $master = $config->findChainContaining('foo:hello');
        self::assertNull($master);
        $master = $config->findChainContaining('baz:goodbye');
        self::assertNull($master);
    }

}
