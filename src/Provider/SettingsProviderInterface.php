<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Provider;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;

interface SettingsProviderInterface
{
    /**
     * Get Settings cache key.
     */
    public function getCacheKey(string $settingId): string;

    /**
     * Get ApplicationSettings Entity.
     */
    public function getSettingsEntity(string $settingId): ?SettingsInterface;

    /**
     * Get Settings data from cache.
     */
    public function getCachedSettings(string $settingId): SettingsInterface;

    /**
     * Load data from array to Settings DTO.
     *
     * @param array<string, mixed> $data
     */
    public function loadSettingsDtoFromArray(string $settingId, array $data): SettingsDtoInterface;

    /**
     * Fetch settings data to provided DTO class.
     */
    public function loadSettingsDto(string $settingId): SettingsDtoInterface;
}
