<?php
/**
 * Created by PhpStorm.
 * User: AlexanderC <self@alexanderc.me>
 * Date: 7/18/14
 * Time: 10:39
 */

namespace AlexanderC\Api\Mashery\Transport\Exception;


class AuthorizationException extends \RuntimeException
{
    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->validate($data);

        parent::__construct($data['error']['message'], $data['error']['code']);
    }

    /**
     * @param array $data
     * @throws \RuntimeException
     */
    protected function validate(array $data)
    {
        if(!isset($data['error'], $data['error']['code'], $data['error']['message'])) {
            throw new \RuntimeException("Wrong authorization error data format");
        }
    }
} 