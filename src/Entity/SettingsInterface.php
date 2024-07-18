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
    public function setId(string $settingsId): self;

    /**
     * Get Settings data.
     *
     * @return array<string, mixed>
     */
    public function getSettings(): array;

    /**
     * Set Settings data.
     *
     * @param array<string, mixed> $settings
     */
    public function setSettings(array $settings): self;
}
