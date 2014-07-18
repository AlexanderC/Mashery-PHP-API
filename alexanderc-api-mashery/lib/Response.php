<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;

use AlexanderC\Api\Mashery\Helpers\Inflector;

class Response
{
    use ResponseDataValidator;

    /**
     * @var array
     */
    protected $attributes = ['result', 'error', 'id'];

    /**
     * @var array|null
     */
    protected $error;

    /**
     * @var null|ErrorObject
     */
    protected $errorObject;

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

        foreach ($this->attributes as $attribute) {
            $this->{$attribute} = $responseData[$attribute];
        }

        if(null !== $this->error) {
            $this->errorObject = $this->error;
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
     * @return mixed
     */
    public function getError()
    {
        return $this->errorObject;
    }

    /**
     * @return array|null
     */
    public function getErrorData()
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
        foreach ($object->getMasherySyncProperties() as $property) {
            if ($object->masheryUseSettersAndGetters()) {
                $setter = sprintf("set%s", Inflector::classify($property));

                $object->$setter($this->result[$property]);
            } else {
                $object->{$property} = $this->result[$property];
            }
        }

        return $object;
    }
}