<?php

declare(strict_types=1);

namespace CreativePoint\SettingsBundle\Tests\Factory;

use CreativePoint\SettingsBundle\Entity\Settings;
use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Factory\SettingsFactory;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use CreativePoint\SettingsBundle\Provider\SettingsProvider;
use CreativePoint\SettingsBundle\Repository\SettingsRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Zfekete\BypassReadonly\BypassReadonly;

class SettingsFactoryUnitTest extends TestCase
{
    private $repositoryMock;
    private $providerMock;
    private $cacheMock;

    protected function setUp(): void
    {
        BypassReadonly::enable();

        $this->providerMock = $this->createMock(SettingsProvider::class);
        $this->repositoryMock = $this->createMock(SettingsRepository::class);
        $this->cacheMock = $this->createMock(CacheInterface::class);
    }

    public function testUpdateEntityAsArray()
    {
        $settingsEntity = (new Settings())
            ->setId('TestSettingsEntity')
            ->setSettings([]);

        $this->providerMock->expects($this->once())
            ->method('getSettingsEntity')
            ->with($settingsEntity->getId())
            ->willReturn($settingsEntity);

        $this->providerMock->expects($this->once())
            ->method('getCacheKey')
            ->with($settingsEntity->getId())
            ->willReturn('settings_TestSettingsEntity');

        $this->repositoryMock->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(Settings::class), true);

        $this->cacheMock->expects($this->once())
            ->method('delete')
            ->with('settings_TestSettingsEntity');

        $factory = new SettingsFactory($this->repositoryMock, $this->providerMock, $this->cacheMock);
        $settings = $factory->setSettingsData($settingsEntity->getId(), ['foo' => 'bar']);

        $this->assertInstanceOf(SettingsInterface::class, $settings);
        $this->assertSame(['foo' => 'bar'], $settingsEntity->getSettings());
    }

    public function testUpdateEntityFromDto()
    {
        $settingsEntity = (new Settings())
            ->setId('TestSettingsEntity')
            ->setSettings([]);

        $this->providerMock->expects($this->once())
            ->method('getSettingsEntity')
            ->with($settingsEntity->getId())
            ->willReturn($settingsEntity);

        $factory = new SettingsFactory($this->repositoryMock, $this->providerMock, $this->cacheMock);
        $settings = $factory->setSettingsDataFromDto(new TestSettingsDto());

        $this->assertInstanceOf(SettingsDtoInterface::class, $settings);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->repositoryMock = null;
        $this->providerMock = null;
        $this->cacheMock = null;
    }
}

class TestSettingsDto implements SettingsDtoInterface
{
    public function __construct(
        public string $foo = 'bar',
    ) {
    }

    public static function getSettingsId(): string
    {
        return 'TestSettingsEntity';
    }
}
