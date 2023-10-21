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
        $config = $builder->getExtensionConfig('chain_command');
        $chains =  $config[0]['chains'] ?? [];
        if (array_key_exists('foo:hello', $chains)) {
            $chains['foo:hello']['members'][] = 'bar:hi';
        } else {
            $chains['foo:hello'] = [
                'members' => [
                    'bar:hi',
                ],
            ];
        }
        $builder->prependExtensionConfig('chain_command', [
            'chains' => $chains,
        ]);
    }
}