<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;

use AlexanderC\Api\Mashery\Definition\DefinitionInterface;
use AlexanderC\Api\Mashery\Transport\AbstractTransport;

class Client
{
    use ExtendedClientTrait;

    /**
     * @var Transport\AbstractTransport
     */
    protected $transport;

    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @var Transformer\TransformerInterface
     */
    protected $transformer;

    /**
     * @var bool
     */
    protected $sandboxMode;

    /**
     * @param AbstractTransport $transport
     * @param Credentials $credentials
     * @param DefinitionInterface $definition
     */
    public function __construct(
        AbstractTransport $transport,
        Credentials $credentials,
        DefinitionInterface $definition)
    {
        $this->transport = $transport;
        $this->credentials = $credentials;
        $this->transformer = $this->createTransformer($definition);

        $transport->init($this->credentials, $definition, $this->transformer);
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return array
     */
    public function call($method, array $parameters)
    {
        $data = [
            'id' => 1, // it is not that important since it's using syncronous calls
            'method' => $method,
            'params' => $parameters
        ];

        return $this->transport->request($data, $this->sandboxMode);
    }

    /**
     * @return \AlexanderC\Api\Mashery\Transformer\TransformerInterface
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @return \AlexanderC\Api\Mashery\Transport\AbstractTransport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @return boolean
     */
    public function isSandboxMode()
    {
        return $this->sandboxMode;
    }

    /**
     * @param boolean $sandbox
     */
    public function setSandboxMode($sandbox)
    {
        $this->sandboxMode = (bool)$sandbox;
    }

    /**
     * @param DefinitionInterface $definition
     * @return \AlexanderC\Api\Mashery\Transformer\TransformerInterface
     */
    protected function createTransformer(DefinitionInterface $definition)
    {
        $transformerClass = sprintf(
            'AlexanderC\Api\Mashery\Transformer\%sTransformer',
            ucfirst($definition->getTransformer())
        );

        return new $transformerClass;
    }
}