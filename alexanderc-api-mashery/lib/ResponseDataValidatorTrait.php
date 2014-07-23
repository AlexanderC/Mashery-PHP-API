<?php
/**
 * Created by PhpStorm.
 * User: AlexanderC <self@alexanderc.me>
 * Date: 7/18/14
 * Time: 15:58
 */

namespace AlexanderC\Api\Mashery;


use AlexanderC\Api\Mashery\Exception\InvalidResponseException;

trait ResponseDataValidatorTrait
{
    /**
     * @param array $data
     * @throws Exception\InvalidResponseException
     */
    protected function validate(array $data)
    {
        foreach ($this->attributes as $attribute) {
            if (!array_key_exists($attribute, $data)) {
                throw new InvalidResponseException("Missing attribute {$attribute} from response");
            }
        }
    }
} 