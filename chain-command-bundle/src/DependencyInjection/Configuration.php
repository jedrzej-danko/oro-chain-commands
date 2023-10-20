<?php

namespace OroChain\ChainCommandBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('chain_command');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('chains')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('members')
                                ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ;
        return $treeBuilder;
    }

}