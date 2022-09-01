<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Tests\Repository;

use CreativePoint\SettingsBundle\Entity\Settings;
use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class SettingsRepositoryUnitTest extends TestCase
{
    protected $registryMock;
    protected $entityManagerMock;

    protected function setUp(): void
    {
        $this->registryMock = $this->createMock(ManagerRegistry::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
    }

    public function testAdd()
    {
        $settings = (new Settings())
            ->setSettingsId('testSettings');

        $this->registryMock->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($this->entityManagerMock);
        $this->entityManagerMock->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($this->createMock(ClassMetadata::class));

        $repository = new SettingsRepository($this->registryMock, SettingsInterface::class);

        $this->entityManagerMock->expects($this->exactly(2))
            ->method('persist')
            ->with($settings);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $repository->add($settings);
        $repository->add($settings, true);
    }

    public function testRemove()
    {
        $settings = (new Settings())
            ->setSettingsId('testSettings');

        $this->registryMock->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($this->entityManagerMock);
        $this->entityManagerMock->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($this->createMock(ClassMetadata::class));

        $repository = new SettingsRepository($this->registryMock, SettingsInterface::class);

        $this->entityManagerMock->expects($this->exactly(2))
            ->method('remove')
            ->with($settings);

        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $repository->remove($settings);
        $repository->remove($settings, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->reistryMock = null;
        $this->entityManagerMock = null;
    }
}
