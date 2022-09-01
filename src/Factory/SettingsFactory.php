<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Factory;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use CreativePoint\SettingsBundle\Provider\SettingsProvider;
use CreativePoint\SettingsBundle\Repository\SettingsRepositoryInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use Symfony\Contracts\Cache\CacheInterface;

class SettingsFactory
{
    public function __construct(
        private readonly SettingsRepositoryInterface $repository,
        private readonly SettingsProvider $provider,
        private readonly CacheInterface $cache,
    ) {
    }

    /**
     * Set Settings data and evict cache.
     */
    public function setSettingsData(string $settingsId, array $settings): SettingsInterface
    {
        $settingsEntity = $this->provider->getSettingsEntity($settingsId);
        $settingsEntity->setSettings($settings);
        $this->repository->add($settingsEntity, true);

        $this->cache->delete($this->provider->getCacheKey($settingsId));

        return $this->provider->getCachedSettings($settingsId);
    }

    /**
     * Set Settings data from DTO and evict cache.
     */
    public function setSettingsDataFromDto(SettingsDtoInterface $dto): SettingsDtoInterface
    {
        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
        $serializer = $serializerBuilder->build();

        $this->setSettingsData($dto->getSettingsId(), $serializer->toArray($dto));

        return $dto;
    }
}
