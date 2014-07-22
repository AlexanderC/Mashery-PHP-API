<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;

use AlexanderC\Api\Mashery\Helpers\Inflector;
use AlexanderC\Api\Mashery\Helpers\ObjectSyncer;

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
     * @var ErrorObject
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
            $this->errorObject = new ErrorObject($this->error);
        }
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return null !== $this->error;
    }

    /**
     * @return ErrorObject
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
        // do not generate errors when no data...
        ObjectSyncer::sync($object, $this->result ? : []);
    }
}