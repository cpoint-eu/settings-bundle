<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Provider;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use CreativePoint\SettingsBundle\Repository\SettingsRepositoryInterface;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class SettingsProvider implements SettingsProviderInterface
{
    public function __construct(
        private CacheInterface $cache,
        private SettingsRepositoryInterface $repository,
        private iterable $settingsDtoDefinitions,
        private string $settingsClass,
        private string $settingsCacheKeyPattern,
        private int $settingsCacheTtl,
    ) {
    }

    /**
     * Get Settings cache key.
     */
    public function getCacheKey(string $settingId): string
    {
        return \sprintf($this->settingsCacheKeyPattern, $settingId);
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
            $this->repository->add(entity: $settings, flush: true);
        }

        return $settings;
    }

    /**
     * Get Settings data from cache.
     *
     * @throws InvalidArgumentException
     */
    public function getCachedSettings(string $settingId): SettingsInterface
    {
        return $this->cache->get(key: $this->getCacheKey($settingId), callback: function (ItemInterface $item) use ($settingId) {
            $item->expiresAfter(time: $this->settingsCacheTtl);

            return $this->getSettingsEntity(settingId: $settingId);
        });
    }

    /**
     * Load data from array to Settings DTO.
     *
     * @throws \Exception
     */
    public function loadSettingsDtoFromArray(string $settingId, array $data): SettingsDtoInterface
    {
        $dto = $this->createSettingsDto(dtoName: $settingId);

        $serializerBuilder = SerializerBuilder::create();
        $serializerBuilder->setPropertyNamingStrategy(propertyNamingStrategy: new IdenticalPropertyNamingStrategy());
        $serializer = $serializerBuilder->build();

        return $serializer->fromArray(data: $data, type: \get_class($dto));
    }

    /**
     * Fetch settings data to provided DTO class.
     *
     * @throws \Exception
     * @throws InvalidArgumentException
     */
    public function loadSettingsDto(string $settingId): SettingsDtoInterface
    {
        $settings = $this->getCachedSettings(settingId: $settingId);

        return $this->loadSettingsDtoFromArray(settingId: $settingId, data: $settings->getSettings());
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

        throw new \Exception(\sprintf('No Settings definition for name %s', $dtoName));
    }
}
