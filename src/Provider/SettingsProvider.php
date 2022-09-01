<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Provider;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use CreativePoint\SettingsBundle\Repository\SettingsRepositoryInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SettingsProvider
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly SettingsRepositoryInterface $repository,
        private readonly iterable $settingsDtoDefinitions,
        private readonly string $settingsClass,
        private readonly string $settingsCacheKeyPattern,
        private readonly int $settingsCacheTtl,
    ) {
    }

    /**
     * Get Settings cache key.
     */
    public function getCacheKey(string $settingId): string
    {
        return sprintf($this->settingsCacheKeyPattern, $settingId);
    }

    /**
     * Get ApplicationSettings Entity.
     */
    public function getSettingsEntity(string $settingId): ?SettingsInterface
    {
        $settings = $this->repository->find($settingId);

        if (!$settings) {
            $settings = new $this->settingsClass();
            $settings->setId($settingId);
            $this->repository->add($settings, true);
        }

        return $settings;
    }

    /**
     * Get Settings data from cache.
     */
    public function getCachedSettings(string $settingId): SettingsInterface
    {
        return $this->cache->get($this->getCacheKey($settingId), function (ItemInterface $item) use ($settingId) {
            $item->expiresAfter($this->settingsCacheTtl);

            return $this->getSettingsEntity($settingId);
        });
    }

    /**
     * Load data from array to Settings DTO.
     */
    public function loadSettingsDtoFromArray(string $settingId, array $data): SettingsDtoInterface
    {
        $dto = $this->createSettingsDto($settingId);

        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());
        $serializer = $serializerBuilder->build();

        return $serializer->fromArray($data, get_class($dto));
    }

    /**
     * Fetch settings data to provided DTO class.
     */
    public function loadSettingsDto(string $settingId): SettingsDtoInterface
    {
        $settings = $this->getCachedSettings($settingId);

        return $this->loadSettingsDtoFromArray($settingId, $settings->getSettings());
    }

    /**
     * Create Settings DTO definition.
     *
     * @throws \Exception
     */
    private function createSettingsDto(string $dtoName): SettingsDtoInterface
    {
        /** @var SettingsDtoInterface $definition */
        foreach ($this->settingsDtoDefinitions as $definition) {
            if ($definition::getSettingsId() === $dtoName) {
                return $definition;
            }
        }

        throw new \Exception(sprintf('No Settings definition for name %s', $dtoName));
    }
}
