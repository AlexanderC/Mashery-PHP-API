<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;


use AlexanderC\Api\Mashery\Definition\DefinitionInterface;

class Credentials
{
    /**
     * @var string
     */
    protected $application;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * @var array
     */
    protected $signatures = [];

    /**
     * @param string $apiKey
     * @param string $secret
     * @param string $application
     */
    public function __construct($apiKey, $secret, $application)
    {
        $this->apiKey = $apiKey;
        $this->secret = $secret;
        $this->application = $application;
    }

    /**
     * @param \AlexanderC\Api\Mashery\Definition\DefinitionInterface $definition
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;
    }

    /**
     * @return \AlexanderC\Api\Mashery\Definition\DefinitionInterface
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return array
     * @throws \RuntimeException
     */
    public function getAuthParameters()
    {
        if (!($this->definition instanceof DefinitionInterface)) {
            throw new \RuntimeException("You should inject definition first");
        }

        return [
            $this->definition->getKeyParameterName() => $this->apiKey,
            $this->definition->getSignatureParameterName() => $this->createSignature()
        ];
    }

    /**
     * @return string
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param string $signature
     * @return bool
     */
    public function isSignatureValid($signature)
    {
        return isset($this->signatures[$signature])
        && ($this->signatures[$signature] + $this->definition->getSignatureLifetime()) < gmdate('U');
    }

    /**
     * @return string
     */
    protected function createSignature()
    {
        $timestamp = gmdate('U');
        $signature = md5($this->apiKey . $this->secret . $timestamp);

        $this->signatures[$signature] = $timestamp;

        return $signature;
    }
}