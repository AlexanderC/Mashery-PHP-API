Mashery-PHP-API
===============

PHP API(v2) library for [Mashery](http://www.mashery.com)

***Resources:***

- [API Specifications](http://support.mashery.com/docs/read/mashery_api/20)

Requirements
============
- PHP 5.4 and higher
- Curl extension

Installation
============
- Via composer: `composer install alexanderc/mashery-php-api`

Usage
=====
- API Methods:
     * `@method Response fetch(string|InternalObjectInterface $objectType, array $parameters = [])`
     * `@method Response create(string|InternalObjectInterface $objectType, array $parameters = [])`
     * `@method Response update(string|InternalObjectInterface $objectType, array $parameters = [])`
     * `@method Response delete(string|InternalObjectInterface $objectType, array $parameters = [])`
     * `@method bool validate(string|InternalObjectInterface $objectType, array $parameters = [], Response &$response)`
     * `@method QueryResponse query(string|MsrQL $query)`
     * `@method array call(string $method, array $parameters)`

- MsrQL Methods
     * `MsrQL select(string $selector)`
     * `MsrQL from(string $object)`
     * `MsrQL where(string $expression)`
     * `MsrQL andWhere(string $expression)`
     * `MsrQL orWhere(string $expression)`
     * `MsrQL descendingOrderBy(string $field)`
     * `MsrQL ascendingOrderBy(string $field)`
     * `MsrQL items(int $count)`
     * `MsrQL page(int $number)`
     * `MsrQL requireRelated(string $object, string $expression)`

- Object Types (for v2 api)
     * `member`
     * `application`
     * `key`
     * `package_key`
     * `package`
     * `plan`
     * `service`
     * `role`
     * `developer_class`

```php
<?php

$apiKey = 'test';
$secret = 'test';
$application = 'alexanderc';
$transportName = 'curl'; // default value
$version = 'version2'; // default value

class TestObject implements \AlexanderC\Api\Mashery\InternalObjectInterface
{
    public $name;

    public function getMasherySyncProperties()
    {
        return ['name'];
    }

    public function getMasheryObjectType()
    {
        return 'role';
    }

    public function masheryUseSettersAndGetters()
    {
        return false;
    }
}

try {
    $mashery = AlexanderC\Api\Mashery\Mashery::create(
        $apiKey, $secret, $application, $transportName, $version
    );

    $testObj = new TestObject();
    $testObj->name = 'testit';

    // create object first
    $response = $mashery->create($testObj);

    if($response->isError()) {
        throw new \RuntimeException("Unable to create mashery object: {$response->getError()->getMessage()}");
    }

    // also you can use Mashery SQL like language for custom queries
    // or build it using MsrQL class
    $query = \AlexanderC\Api\Mashery\MsrQL::create();
    $query->from('role');
    $query->where('name = "testit"');

    // you should get result here
    var_dump($mashery->query($query));
} catch (\Exception $e) {
    echo "\n\nException!!!!\n", $e->getMessage(), "\n\n";
}
```

Integrating with entities (ORM and everything else)
===================================================
In order to integrate and use your objects as native ones- you should implement `InternalObjectInterface` interface

***Note: By calling any methods except query and validate using InternalObjectInterface as first argument- you'll get objects syncronized automatically***

TODO
====
- Add tests
- Implement version 3
- Build Symfony2 bundle