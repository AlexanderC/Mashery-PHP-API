<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;

use AlexanderC\Api\Mashery\Transport\AbstractTransport;

class Mashery
{
    const DEFAULT_VERSION = 'version2';

    /**
     * @var \AlexanderC\Api\Mashery\Definition\DefinitionInterface
     */
    protected $definition;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @param Credentials $credentials
     * @param AbstractTransport $transport
     * @param string $version
     */
    public function __construct(
        Credentials $credentials,
        AbstractTransport $transport,
        $version = self::DEFAULT_VERSION)
    {
        $this->definition = $this->createVersionDefinition($version);
        $this->credentials = $credentials;
        $this->client = new Client($transport, $this->credentials, $this->definition);

        $this->credentials->setDefinition($this->definition);
    }

    /**
     * @param string $apiKey
     * @param string $secret
     * @param string $application
     * @param string $transportName
     * @param string $version
     * @return Mashery
     */
    public static function create($apiKey, $secret, $application, $transportName = 'curl', $version = self::DEFAULT_VERSION)
    {
        $transportClass = sprintf('AlexanderC\Api\Mashery\Transport\%sTransport', ucfirst($transportName));
        $transport = new $transportClass;

        $credentials = new Credentials($apiKey, $secret, $application);

        return new self($credentials, $transport, $version);
    }

    /**
     * @return \AlexanderC\Api\Mashery\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return \AlexanderC\Api\Mashery\Credentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return \AlexanderC\Api\Mashery\Definition\DefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param string $version
     * @return \AlexanderC\Api\Mashery\Definition\DefinitionInterface
     */
    protected function createVersionDefinition($version)
    {
        $definitionClass = sprintf('AlexanderC\Api\Mashery\Definition\%sDefinition', ucfirst($version));

        return new $definitionClass;
    }
}