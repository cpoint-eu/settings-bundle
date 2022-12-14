<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Entity;

interface SettingsInterface
{
    public const RESOURCE_KEY = 'application_settings';

    /**
     * Get Settings ID.
     */
    public function getId(): ?string;

    /**
     * Set Settings key.
     */
    public function setId(string $settingsId): SettingsInterface;

    /**
     * Get Settings data.
     */
    public function getSettings(): array;

    /**
     * Set Settings data.
     */
    public function setSettings(?array $settings): SettingsInterface;
}
