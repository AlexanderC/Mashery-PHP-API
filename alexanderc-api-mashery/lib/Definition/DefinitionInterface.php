<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery\Definition;

interface DefinitionInterface
{
    /**
     * Production server url
     *
     * @return string
     */
    public function getServer();

    /**
     * Sandbox server url
     *
     * @return string
     */
    public function getSandbox();

    /**
     * Api key parameter name to be added to each request
     *
     * @return string
     */
    public function getKeyParameterName();

    /**
     * Api signature parameter name to be added to each request
     *
     * @var string
     */
    public function getSignatureParameterName();

    /**
     * Api signature lifetime
     *
     * @return int
     */
    public function getSignatureLifetime();

    /**
     * Human readable API version
     *
     * @return string
     */
    public function getHumanVersion();

    /**
     * Version specific data transformer (ex. json)
     *
     * @return string
     */
    public function getTransformer();

    /**
     * @return array
     */
    public function getObjectTypes();
}