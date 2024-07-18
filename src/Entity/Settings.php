<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Entity;

class Settings implements SettingsInterface
{
    protected ?string $id = null;

    /**
     * @var array<string, mixed>
     */
    protected array $settings = [];

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $settingsId): self
    {
        $this->id = $settingsId;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSettings(): array
    {
        return $this->settings;
    }

    /**
     * @param array<string, mixed> $settings
     */
    public function setSettings(array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }
}
