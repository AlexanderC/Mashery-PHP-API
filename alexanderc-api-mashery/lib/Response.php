<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;

use AlexanderC\Api\Mashery\Exception\InvalidResponseException;
use AlexanderC\Api\Mashery\Helpers\Inflector;

class Response
{
    /**
     * @var array
     */
    protected $attributes = ['result', 'error', 'id'];

    /**
     * @var array|null
     */
    protected $error;

    /**
     * @var array|bool|null
     */
    protected $result;

    /**
     * @var int
     */
    protected $id;

    /**
     * @param array $responseData
     */
    public function __construct(array $responseData)
    {
        $this->validate($responseData);

        foreach($this->attributes as $attribute) {
            $this->{$attribute} = $responseData[$attribute];
        }
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return null === $this->error;
    }

    /**
     * @return array|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array|bool|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param InternalObjectInterface $object
     * @return InternalObjectInterface
     */
    public function sync(InternalObjectInterface $object)
    {
        foreach($object->getMasherySyncProperties() as $property) {
            if($object->masheryUseSettersAndGetters()) {
                $setter = sprintf("set%s", Inflector::classify($property));

                $object->$setter($this->result[$property]);
            } else {
                $object->{$property} = $this->result[$property];
            }
        }

        return $object;
    }

    /**
     * @param array $responseData
     * @throws Exception\InvalidResponseException
     */
    protected function validate(array $responseData)
    {
        foreach($this->attributes as $attribute) {
            if(!array_key_exists($attribute, $responseData)) {
                throw new InvalidResponseException("Missing attribute {$attribute} from response");
            }
        }
    }
}