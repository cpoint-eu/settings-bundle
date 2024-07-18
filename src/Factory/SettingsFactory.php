<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Factory;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use CreativePoint\SettingsBundle\Provider\SettingsProvider;
use CreativePoint\SettingsBundle\Repository\SettingsRepositoryInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
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
     * @param array<string, mixed> $settings
     *
     * @throws InvalidArgumentException
     */
    public function setSettingsData(string $settingsId, array $settings): SettingsInterface
    {
        /** @var SettingsInterface $settingsEntity */
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
     * @throws ExceptionInterface
     */
    public function setSettingsDataFromDto(SettingsDtoInterface $dto): SettingsDtoInterface
    {
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer(normalizers: [$normalizer]);

        /** @var array<string, mixed> $settings */
        $settings = $serializer->normalize(data: $dto);

        $this->setSettingsData(settingsId: $dto->getSettingsId(), settings: $settings);

        return $dto;
    }
}
