<?php

namespace OroChain\ChainCommandBundle\DependencyInjection;


use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ChainCommandExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container, new FileLocator(__DIR__ . '/../../config')
        );
        $loader->load('services.yml');

        $container->getParameterBag()->set('chain_command_bundle.chains', $configs[0]['chains']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $config = [
            'channels' => ['chained_command'],
            'handlers' => [
                'chained_command' => [
                    'type' => 'stream',
                    'path' => '%kernel.logs_dir%/chained_command.log',
                    'level' => 'debug',
                    'formatter' => 'chain_command_bundle.log_formatter',
                    'channels' => ['chained_command']
                ]
            ],
        ];

        if ($container->hasExtension('monolog')) {
            $container->prependExtensionConfig('monolog', $config);
        }
    }


}