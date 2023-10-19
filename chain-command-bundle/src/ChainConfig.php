<?php

namespace OroChain\ChainCommandBundle;

use OroChain\ChainCommandBundle\DependencyInjection\Configuration;
use OroChain\ChainCommandBundle\Dto\ChainDto;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ChainConfig
{
    /** @var ChainDto[] */
    private array $chains = [];


    public function __construct(ParameterBagInterface $parameterBag)
    {
        foreach ($parameterBag->get('chain_command_bundle.chains') as $chain) {
            $this->chains[] = new ChainDto($chain['startsWith'], $chain['then']);
        }
    }

    public function getChains(): array
    {
        return $this->chains;
    }

    public function getChainForCommand(string $command) : ?array
    {
        foreach ($this->chains as $chain) {
            if ($chain->startsWith === $command) {
                return $chain->chain;
            }
        }
        return null;
    }

    public function isChainMember(string $commandName): bool
    {
        foreach ($this->chains as $chainedCommands) {
            if (in_array($commandName, $chainedCommands->chain)) {
                return true;
            }
        }
        return false;
    }
}