<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\DependencyInjection;

use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class CreativePointSettingsExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(container: $container, locator: new FileLocator(paths: __DIR__ . '/../Resources/config'));
        $loader->load(resource: 'services.xml');

        $configuration = $this->getConfiguration(config: $configs, container: $container);
        $config = $this->processConfiguration(configuration: $configuration, configs: $configs);

        $container->registerForAutoconfiguration(interface: SettingsDtoInterface::class)
            ->addTag(name: 'creative_point_settings_model');

        $settingsProviderDefinition = $container->getDefinition(id: 'creative_point_settings.provider.settings_provider');
        if (null !== $config['objects']['repository']) {
            $container->setAlias(alias: 'creative_point_settings.repository', id: $config['objects']['repository']);
        }
        $settingsProviderDefinition->setArgument(key: 3, value: $config['objects']['model']);
        $settingsProviderDefinition->setArgument(key: 4, value: $config['cache_key']);
        $settingsProviderDefinition->setArgument(key: 5, value: $config['cache_ttl']);

        $settingsRepositoryDefinition = $container->getDefinition(id: 'creative_point_settings.repository.settings');
        $settingsRepositoryDefinition->setArgument(key: 1, value: $config['objects']['model']);

        $metadataSubscriberDefinition = $container->getDefinition(id: 'creative_point_settings.doctrine.metadata_subscriber');
        $metadataSubscriberDefinition->setArgument(key: 0, value: $config['objects']['model']);
        $metadataSubscriberDefinition->setArgument(key: 1, value: $config['objects']['repository']);
        $metadataSubscriberDefinition->setArgument(key: 2, value: $config['objects']['table_name']);
    }
}
