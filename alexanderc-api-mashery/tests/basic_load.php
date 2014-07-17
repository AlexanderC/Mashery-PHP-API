<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

require __DIR__ . '/../bootstrap.php';

$apiKey = '';
$secret = '';
$application = '';
$transportName = 'curl'; // default value
$version = 'version2'; // default value

$mashery = AlexanderC\Api\Mashery\Mashery::create(
    $apiKey, $secret, $application, $transportName, $version
);