<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Factory;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use Psr\Cache\InvalidArgumentException;

interface SettingsFactoryInterface
{
    /**
     * Set Settings data and evict cache.
     *
     * @param array<string, mixed> $settings
     *
     * @throws InvalidArgumentException
     */
    public function setSettingsData(string $settingsId, array $settings): SettingsInterface;

    /**
     * Set Settings data from DTO and evict cache.
     *
     * @throws InvalidArgumentException
     */
    public function setSettingsDataFromDto(SettingsDtoInterface $dto): SettingsDtoInterface;
}
