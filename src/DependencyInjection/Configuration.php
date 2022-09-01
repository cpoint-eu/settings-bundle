<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\DependencyInjection;

use CreativePoint\SettingsBundle\Entity\Settings;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('creative_point_settings');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->scalarNode('cache_key')->defaultValue('settings_%s')->end()
            ->integerNode('cache_ttl')->defaultValue(604800)->end()
            ->arrayNode('objects')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('table_name')->defaultValue('cp_settings')->end()
                    ->scalarNode('model')->defaultValue(Settings::class)->end()
                    ->scalarNode('repository')->defaultValue(null)->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
