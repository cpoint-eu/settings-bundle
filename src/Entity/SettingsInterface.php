<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Entity;

interface SettingsInterface
{
    public const RESOURCE_KEY = 'application_settings';

    /**
     * Get Settings ID.
     */
    public function getId(): mixed;

    /**
     * Get Settings key.
     */
    public function getSettingsId(): ?string;

    /**
     * Set Settings key.
     */
    public function setSettingsId(string $settingsId): SettingsInterface;

    /**
     * Get Settings data.
     */
    public function getSettings(): array;

    /**
     * Set Settings data.
     */
    public function setSettings(?array $settings): SettingsInterface;
}
