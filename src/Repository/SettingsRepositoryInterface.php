<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Repository;

use CreativePoint\SettingsBundle\Entity\Settings;

interface SettingsRepositoryInterface
{
    /**
     * Create or update the Settings entity.
     */
    public function add(Settings $entity, bool $flush = false): void;

    /**
     * Remove Settings.
     */
    public function remove(Settings $entity, bool $flush = false): void;
}
