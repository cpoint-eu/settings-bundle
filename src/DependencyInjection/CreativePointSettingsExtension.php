<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\DependencyInjection;

use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CreativePointSettingsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $container->registerForAutoconfiguration(SettingsDtoInterface::class)
            ->addTag('creative_point_settings_model');

        $settingsProviderDefinition = $container->getDefinition('creative_point_settings.provider.settings_provider');
        if (null !== $config['objects']['repository']) {
            $container->setAlias('creative_point_settings.repository', $config['objects']['repository']);
        }
        $settingsProviderDefinition->setArgument(3, $config['objects']['model']);
        $settingsProviderDefinition->setArgument(4, $config['cache_key']);
        $settingsProviderDefinition->setArgument(5, $config['cache_ttl']);

        $settingsRepositoryDefinition = $container->getDefinition('creative_point_settings.repository.settings');
        $settingsRepositoryDefinition->setArgument(1, $config['objects']['model']);

        $metadataSubscriberDefinition = $container->getDefinition('creative_point_settings.doctrine.metadata_subscriber');
        $metadataSubscriberDefinition->setArgument(0, $config['objects']['model']);
        $metadataSubscriberDefinition->setArgument(1, $config['objects']['repository']);
        $metadataSubscriberDefinition->setArgument(2, $config['objects']['table_name']);
    }
}
