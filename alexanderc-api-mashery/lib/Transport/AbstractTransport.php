<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery\Transport;

use AlexanderC\Api\Mashery\Credentials;
use AlexanderC\Api\Mashery\Definition\DefinitionInterface;
use AlexanderC\Api\Mashery\Transformer\TransformerInterface;

abstract class AbstractTransport
{
    const CLIENT_STRING = 'alexanderc_api_call/0.1b';

    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @var DefinitionInterface
     */
    protected $definition;

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * @var bool
     */
    protected $init = false;

    /**
     * @param Credentials $credentials
     * @param DefinitionInterface $definition
     * @param TransformerInterface $transformer
     */
    public function init(
        Credentials $credentials,
        DefinitionInterface $definition,
        TransformerInterface $transformer)
    {
        $this->credentials = $credentials;
        $this->definition = $definition;
        $this->transformer = $transformer;

        $this->init = true;
    }

    /**
     * @param array $data
     * @param bool $sandboxMode
     * @return array
     * @throws \RuntimeException
     */
    public function request(array $data, $sandboxMode = false)
    {
        if (false === $this->init) {
            throw new \RuntimeException("You should inject services first");
        }

        $url = sprintf(
            "%s/%s?%s",
            rtrim($sandboxMode ? $this->definition->getSandbox() : $this->definition->getServer(), "/"),
            $this->credentials->getApplication(),
            http_build_query($this->credentials->getAuthParameters())
        );
        $body = $this->transformer->encode($data);

        $headers = [
            'Content-Type: application/json',
            'X-Api-Client: ' . self::CLIENT_STRING,
            'Content-Length: ' . strlen($body) . "\r\n",
            $body
        ];

        return $this->transformer->decode($this->__request($url, $headers));
    }

    /**
     * @param string $url
     * @param array $headers
     * @return string
     * @throws Exception\TransportException
     */
    abstract protected function __request($url, array $headers);
}