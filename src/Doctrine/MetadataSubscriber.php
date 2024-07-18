<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Doctrine;

use CreativePoint\SettingsBundle\Entity\Settings;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

readonly class MetadataSubscriber
{
    /**
     * @param class-string<EntityRepository<Settings>>|null $settingsCustomRepository
     */
    public function __construct(
        private ?string $settingsModel,
        private ?string $settingsCustomRepository,
        private ?string $settingsTableName,
    ) {
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event): void
    {
        /** @var ClassMetadata $metadata */
        $metadata = $event->getClassMetadata();
        if (Settings::class !== $metadata->getName()) {
            return;
        }

        $table = $metadata->table;
        $table['name'] = $this->settingsTableName;
        $metadata->setPrimaryTable(table: $table);

        if (isset($this->settingsModel) && $this->settingsModel === $metadata->getName()) {
            $metadata->isMappedSuperclass = false;

            if (isset($this->settingsCustomRepository)) {
                $metadata->setCustomRepositoryClass(repositoryClassName: $this->settingsCustomRepository);
            }
        }
    }
}
