<?php

namespace OroChain\ChainCommandBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ChainCommandExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(__DIR__ . '/../../config')
        );
        $loader->load('services.yml');

        $container->getParameterBag()->set('chain_command_bundle.chains', $configs[0]['chains']);
    }



}