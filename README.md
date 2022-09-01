# Information

CreativePointSettingsBundle manages configurations settings int the database and make them available via DTO objects.

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require cpoint-eu/setting-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require cpoint-eu/setting-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    CreativePoint\SettingsBundle\CreativePointSettingsBundle::class => ['all' => true],
];
```

Configuration
=============

The bundle configuration is optional and in most cases does not need to be changed in any way.

```yaml
creative_point_settings:
  # [settings_%s] Settings cache key. %s is replaced by the setting name
  cache_key: 'your_cache_key_%s'

  # [604800] cache TTL
  cache_ttl: 400
  
  objects:
    # [cp_settings] table name in the database  
    table_name: 'your_table_name'

    # [CreativePoint\SettingsBundle\Entity\Settings] your settings entity
    model: 'APP\Entity\Settings'

    # [null] your custom settings entity repository
    repository: 'APP\Repository\Settings'
```

Usage
=====

You must create a DTO object that implements `CreativePoint\SettingsBundle\Model\SettingsDtoInterface`.

```php
//...
use CreativePoint\SettingsBundle\Model\SettingsDtoInterface;

class MySettingsDto implements SettingsDtoInterface
{
    private const SETTINGS_ID = 'mySettings';

    public function __construct(
        public ?string $someValue = 'default value',
        public ?int $someNumber = 254,
    ) {
    }

    public function getSettingsId(): string
    {
        return self::SETTINGS_ID;
    }
}
```

Save settings data
------------------

```php
// ...
use CreativePoint\SettingsBundle\Factory\SettingsFactory;

// ...

// Save settings to the database
public function saveSettings(SettingFactory $factory)
{
    // Set data from array
    $factory->setSettingsData('mySettings', [
        'someValue' => 'new value',
        'someNumber' => 123,
    ]);
    
    // Set data from DTO
    $dto = new MySettingsDto(
        'new value',
        123,
    );
    
    $factory->setSettingsDataFromDto($dto);
}

```

Load settings data
------------------

```php
// ...
use CreativePoint\SettingsBundle\Provider\SettingsProvider;

// ...

// Save settings to the database
public function loadSettings(SettingsProvider $provider)
{
    // Load data from DB by DTO::SETTINGS_ID and return DTO
    $settings = $provider->loadSettingsDto('mySettings');
    // ...or load SettingsEntity itself
    $settings = $provider->getSettingsEntity('mySettings');
    
    // You can also load your DTO from array data
    $settings = $provider->loadSettingsDtoFromArray('mySettings', [
        'someValue' => 'new value',
        'someNumber' => 123,
    ]);
}

```

Override settings entity
========================

The settings entity can be overridden if necessary. The newly created entity must extend the base Settings entity 
`CreativePoint\SettingsBundle\Entity\Settings` or implement `CreativePoint\SettingsBundle\Entity\SettingsInterface`. 
Then you need to modify the bundle configuration:

```yaml
creative_point_settings:
  objects:
    model: 'APP\Entity\YourSettingsEntity'
```

You can replace SettingsRepository in the same way. Your new repository must extend 
`CreativePoint\SettingsBundle\Repository\SettingsRepository` or implement 
`CreativePoint\SettingsBundle\Repository\SettingsRepositoryInterface`. And make the bundle aware of it:

```yaml
creative_point_settings:
  objects:
    repository: 'APP\Repository\YourSettingsRepository'
```
