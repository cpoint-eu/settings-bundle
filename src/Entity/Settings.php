<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Entity;

class Settings implements SettingsInterface
{
    private mixed $id = null;
    private ?string $settingsId = null;
    private array $settings = [];

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getSettingsId(): ?string
    {
        return $this->settingsId;
    }

    public function setSettingsId(string $settingsId): self
    {
        $this->settingsId = $settingsId;

        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(?array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }
}
