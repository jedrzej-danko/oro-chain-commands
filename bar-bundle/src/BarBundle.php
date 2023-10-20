<?php
namespace OroChain\BarBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class BarBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yml');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$builder->hasExtension('chain_command')) {
            return;
        }
        $builder->prependExtensionConfig('chain_command', [
            'chains' => [
                'foo:hello' => [
                    'members' => [
                        'bar:hi',
                    ],
                ],
            ],
        ]);
    }
}