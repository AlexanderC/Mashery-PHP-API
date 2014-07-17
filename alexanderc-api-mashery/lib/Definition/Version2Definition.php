<?php

/** 
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery\Definition;

class Version2Definition implements DefinitionInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getServer()
	{
		return "http://api.mashery.com/v2/json-rpc";
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSandbox()
	{
		return "http://api.sandbox.mashery.com/v2/json-rpc";
	}

	/**
	 * {@inheritdoc}
	 */
	public function getKeyParameterName()
	{
		return "apikey";
	}

	/**
	 * {@inheritdoc}
	 */
	public function getSignatureParameterName()
	{
		return "sig";
	}

    /**
     * Api signature lifetime
     *
     * @return int
     */
    public function getSignatureLifetime()
    {
        return 300; // 5 minutes
    }

    /**
	 * {@inheritdoc}
	 */
	public function getHumanVersion()
	{
		return "Mashery API Version 2";
	}

    /**
     * {@inheritdoc}
     */
    public function getTransformer()
    {
        return 'json';
    }

    /**
     * @return array
     */
    public function getObjectTypes()
    {
        return [
            'member', 'application', 'key',
            'package_key', 'package', 'plan',
            'service', 'role'
        ];
    }
}