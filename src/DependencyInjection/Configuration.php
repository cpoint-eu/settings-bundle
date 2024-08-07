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
        $treeBuilder = new TreeBuilder(name: 'creative_point_settings');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->scalarNode(name: 'cache_key')->defaultValue(value: 'settings_%s')->end()
            ->integerNode(name: 'cache_ttl')->defaultValue(value: 604800)->end()
            ->arrayNode(name: 'objects')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode(name: 'table_name')->defaultValue(value: 'cp_settings')->end()
                    ->scalarNode(name: 'model')->defaultValue(value: Settings::class)->end()
                    ->scalarNode(name: 'repository')->defaultValue(value: null)->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
