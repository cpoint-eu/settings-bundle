<?php

declare(strict_types=1);

use CreativePoint\SettingsBundle\Entity\Settings;
use CreativePoint\SettingsBundle\Entity\SettingsInterface;
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;
use CreativePoint\SettingsBundle\Provider\SettingsProvider;
use CreativePoint\SettingsBundle\Repository\SettingsRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

class SettingsProviderUnitTest extends TestCase
{
    private ?MockObject $cacheMock;
    private ?MockObject $repositoryMock;
    private ?string $settingsCacheKeyPattern = 'settings_%s';

    public function setUp(): void
    {
        $this->cacheMock = $this->createMock(CacheInterface::class);
        $this->repositoryMock = $this->createMock(SettingsRepository::class);
    }

    public function testGetCacheKey()
    {
        $dtoSettingsId = 'someSettingsId';
        $provider = new SettingsProvider(
            $this->cacheMock,
            $this->repositoryMock,
            [],
            Settings::class,
            $this->settingsCacheKeyPattern,
            640
        );
        $cacheKey = $provider->getCacheKey($dtoSettingsId);

        $this->assertIsString($cacheKey);
        $this->assertSame(sprintf($this->settingsCacheKeyPattern, $dtoSettingsId), $cacheKey);
    }

    /**
     * @dataProvider getEntitySpecificationTests
     */
    public function testGetSettingsEntity(string $settingsId, ?SettingsInterface $settings)
    {
        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with($settingsId)
            ->willReturn($settings);

        $provider = new SettingsProvider(
            $this->cacheMock,
            $this->repositoryMock,
            [],
            Settings::class,
            $this->settingsCacheKeyPattern,
            640
        );

        if (!$settings) {
            $this->repositoryMock->expects($this->once())
                ->method('add')
                ->with($this->isInstanceOf(Settings::class), true);
        }

        $settingsEntity = $provider->getSettingsEntity($settingsId);

        $this->assertInstanceOf(Settings::class, $settingsEntity);
        $this->assertSame($settingsEntity->getId(), $settingsId);
        $this->assertSame($settingsEntity->getSettings(), []);
    }

    /**
     * @dataProvider getDtoSpecificationTests
     */
    public function testLoadSettingsDtoFromArray(string $name, iterable $definitions, array $data)
    {
        $provider = new SettingsProvider(
            $this->cacheMock,
            $this->repositoryMock,
            $definitions,
            Settings::class,
            $this->settingsCacheKeyPattern,
            640
        );

        if (empty($definitions)) {
            $this->expectException(Exception::class);
        }

        $dto = $provider->loadSettingsDtoFromArray($name, $data);
        $this->assertInstanceOf(SettingsDtoInterface::class, $dto);
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $this->assertArrayHasKey($key, (array) $dto);
                $this->assertSame($value, $dto->{$key});
            } else {
                $this->assertArrayNotHasKey($key, (array) $dto);
            }
        }

    }

    public function testLoadSettingsDto()
    {
        $this->cacheMock->expects($this->once())
            ->method('get')
            ->with(sprintf($this->settingsCacheKeyPattern, TestSettingsDto::getSettingsId()))
            ->willReturn((new Settings())
                ->setId(TestSettingsDto::getSettingsId())
                ->setSettings([])
            );

        $provider = new SettingsProvider(
            $this->cacheMock,
            $this->repositoryMock,
            [new TestSettingsDto()],
            Settings::class,
            $this->settingsCacheKeyPattern,
            640
        );

        $dto = $provider->loadSettingsDto(TestSettingsDto::getSettingsId());
        $this->assertInstanceOf(SettingsDtoInterface::class, $dto);
        $this->assertInstanceOf(TestSettingsDto::class, $dto);
    }

    public function getEntitySpecificationTests(): array
    {
        $settingsInDB = (new Settings())
            ->setId('testSettingsId')
            ->setSettings([]);

        return [
            // name, Setting object
            ['testSettingsId', $settingsInDB],
            ['testSettingsNotInDB', null],
        ];
    }

    public function getDtoSpecificationTests(): array
    {
        return [
            // name, DTO definitions, data
            ['testDTO', [], []],
            ['testDTO', [new TestSettingsDto()], ['foo' => 'foo', ' bar' => 'bar', ' baz' => true]],
            ['testDTO', [new TestSettingsDto()], []],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cacheMock = null;
        $this->repositoryMock = null;
        $this->settingsCacheKeyPattern = null;
    }
}

class TestSettingsDto implements SettingsDtoInterface
{
    public function __construct(
        public string $foo = 'bar',
        public bool $baz = false,
    ) {
    }

    public static function getSettingsId(): string
    {
        return 'testDTO';
    }
}
