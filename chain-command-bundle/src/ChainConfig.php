<?php

namespace OroChain\ChainCommandBundle;

use OroChain\ChainCommandBundle\Dto\ChainDto;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ChainConfig
{
    /** @var array<string, string[]> */
    private array $chains = [];


    public function __construct(ParameterBagInterface $parameterBag)
    {
        foreach ($parameterBag->get('chain_command_bundle.chains') as $master => $chain) {
            $this->chains[$master] = $chain['members'];
        }
    }

    /**
     * Returns chain initiated by the given command
     *
     * @param string $command
     * @return string[]|null Command chain members
     */
    public function getChainForCommand(string $command) : ?array
    {
        return $this->chains[$command] ?? null;
    }

    /**
     * Returns master command for the command found in chain
     *
     * @param string $commandName
     * @return string|null Name of the master command
     */
    public function findChainContaining(string $commandName) : ?string
    {
        foreach ($this->chains as $masterCommand => $chainedCommands) {
            if (in_array($commandName, $chainedCommands)) {
                return $masterCommand;
            }
        }
        return null;
    }
}