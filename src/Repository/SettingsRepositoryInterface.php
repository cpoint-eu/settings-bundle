<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Repository;

use CreativePoint\SettingsBundle\Entity\SettingsInterface;

/**
 * @method SettingsInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method SettingsInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method SettingsInterface[] findAll()
 * @method SettingsInterface[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface SettingsRepositoryInterface
{
    /**
     * Create or update the Settings entity.
     */
    public function add(SettingsInterface $entity, bool $flush = false): void;

    /**
     * Remove Settings.
     */
    public function remove(SettingsInterface $entity, bool $flush = false): void;
}
