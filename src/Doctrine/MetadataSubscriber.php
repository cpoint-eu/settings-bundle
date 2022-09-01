<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Doctrine;

use CreativePoint\SettingsBundle\Entity\Settings;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class MetadataSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly ?string $settingsModel,
        private readonly ?string $settingsCustomRepository,
        private readonly ?string $settingsTableName,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $event)
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $event->getClassMetadata();
        if (Settings::class !== $metadata->getName()) {
            return;
        }

        $table = $metadata->table;
        $table['name'] = $this->settingsTableName;
        $metadata->setPrimaryTable($table);

        if (isset($this->settingsModel) && $this->settingsModel === $metadata->getName()) {
            $metadata->isMappedSuperclass = false;

            if (isset($this->settingsCustomRepository)) {
                $metadata->setCustomRepositoryClass($this->settingsCustomRepository);
            }
        }
    }
}
