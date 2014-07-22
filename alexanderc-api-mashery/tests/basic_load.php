<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

require __DIR__ . '/../bootstrap.php';

$apiKey = 'test';
$secret = 'test';
$application = 'alexanderc';
$transportName = 'curl'; // default value
$version = 'version2'; // default value

try {
    $mashery = AlexanderC\Api\Mashery\Mashery::createInstance(
        $apiKey, $secret, $application, $transportName, $version
    );

    $query = \AlexanderC\Api\Mashery\MsrQL::create();
    $query->from('role');
    $query->where('name = "smth" AND count IS NOT NULL');
    $query->orWhere('name NOT "other"');
    $query->descendingOrderBy("name");
    $query->items(5);
    $query->page(1);
    $query->requireRelated('application', 'id >= 33 AND name NOT LIKE "denied"');

    var_dump($mashery->query($query));
} catch (\Exception $e) {
    echo "\n\nException!!!!\n", $e->getMessage(), "\n\n";
}