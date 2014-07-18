<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;


class ErrorObject
{
    use ResponseDataValidator;

    /**
     * @var array
     */
    protected $attributes = ['message', 'code', 'data'];

    /**
     * @var string
     */
    protected $message;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $errorData
     */
    public function __construct(array $errorData)
    {
        $this->validate($errorData);

        foreach ($this->attributes as $attribute) {
            $this->{$attribute} = $errorData[$attribute];
        }

    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
} 