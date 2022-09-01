<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\DependencyInjection\Compiler;

use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PersistenceCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    #[NoReturn]
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasExtension('doctrine')) {
            $container->prependExtensionConfig(
                'doctrine',
                [
                    'orm' => [
                        'mappings' => [
                            'CreativePointSettingsBundle' => [
                                'is_bundle' => false,
                                'type' => 'xml',
                                'dir' => __DIR__.'/../../Resources/config/doctrine',
                                'prefix' => 'CreativePoint\SettingsBundle\Entity',
                                'alias' => 'CreativePointSettingsBundle',
                            ],
                        ],
                    ],
                ]
            );
        }
    }
}
