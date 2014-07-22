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
     * `Response fetch(string|InternalObjectInterface $objectType, array $parameters = [])`
     * `Response create(string|InternalObjectInterface $objectType, array $parameters = [])`
     * `Response update(string|InternalObjectInterface $objectType, array $parameters = [])`
     * `Response delete(string|InternalObjectInterface $objectType, array $parameters = [])`
     * `bool validate(string|InternalObjectInterface $objectType, array $parameters = [], Response &$response)`
     * `QueryResponse query(string|MsrQL $query)`
     * `array call(string $method, array $parameters)`

- MsrQL Methods (Mashery Query Language)
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
    $mashery = AlexanderC\Api\Mashery\Mashery::createInstance(
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
    $query = \AlexanderC\Api\Mashery\MsrQL::create()
                ->from('role'); // will be automatically transformed to roles
                ->where('name = "testit"')
    ;

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

Features
========
- Easy to use and customize
- Nearly zero configuration
- No dependencies
- Well documented
- Query builder with some advanced validation to avoid extra API calls
- Automated pluralization of object types in query builder
- Object <-> Mashery sync (with flexible property mapping)
- ...

TODO
====
- Add tests
- Implement API version 3
- <del>Build Symfony2 bundle</del> [Here it is ;)](https://github.com/AlexanderC/Mashery-PHP-API-Bundle)