<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Factory;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use CreativePoint\SettingsBundle\Provider\SettingsProvider;
use CreativePoint\SettingsBundle\Repository\SettingsRepositoryInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

readonly class SettingsFactory implements SettingsFactoryInterface
{
    public function __construct(
        private SettingsRepositoryInterface $repository,
        private SettingsProvider $provider,
        private CacheInterface $cache,
    ) {
    }

    /**
     * Set Settings data and evict cache.
     *
     * @throws InvalidArgumentException
     */
    public function setSettingsData(string $settingsId, array $settings): SettingsInterface
    {
        $settingsEntity = $this->provider->getSettingsEntity(settingId: $settingsId);
        $settingsEntity->setSettings(settings: $settings);
        $this->repository->add(entity: $settingsEntity, flush: true);

        $this->cache->delete(key: $this->provider->getCacheKey(settingId: $settingsId));

        return $this->provider->getCachedSettings(settingId: $settingsId);
    }

    /**
     * Set Settings data from DTO and evict cache.
     *
     * @throws InvalidArgumentException
     */
    public function setSettingsDataFromDto(SettingsDtoInterface $dto): SettingsDtoInterface
    {
        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->setPropertyNamingStrategy(propertyNamingStrategy: new IdenticalPropertyNamingStrategy());
        $serializer = $serializerBuilder->build();

        $this->setSettingsData(settingsId: $dto->getSettingsId(), settings: $serializer->toArray($dto));

        return $dto;
    }
}
