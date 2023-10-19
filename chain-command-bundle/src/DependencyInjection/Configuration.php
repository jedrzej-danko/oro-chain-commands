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
                            ->scalarNode('startsWith')->end()
                            ->arrayNode('then')
                                ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
//            ->arrayNode('chains')
//
            ;
        return $treeBuilder;
    }

}