<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Model;

interface SettingsDtoInterface
{
    /**
     * Get settings Id.
     */
    public static function getSettingsId(): string;
}
