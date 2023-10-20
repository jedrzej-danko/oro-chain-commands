<?php

namespace OroChain\ChainCommandBundle;

use OroChain\ChainCommandBundle\Dto\ChainDto;
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

    /**
     * Returns chain initiated by the given command
     *
     * @param string $command
     * @return ChainDto|null
     */
    public function getChainForCommand(string $command) : ?ChainDto
    {
        foreach ($this->chains as $chain) {
            if ($chain->startsWith === $command) {
                return $chain;
            }
        }
        return null;
    }

    /**
     * Returns first chain that contains the given command
     *
     * @param string $commandName
     * @return ChainDto|null
     */
    public function findChainContaining(string $commandName) : ?ChainDto
    {
        foreach ($this->chains as $chainedCommands) {
            if (in_array($commandName, $chainedCommands->chain)) {
                return $chainedCommands;
            }
        }
        return null;
    }
}