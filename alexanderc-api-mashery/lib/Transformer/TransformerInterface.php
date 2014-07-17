<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery\Transformer;

interface TransformerInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function encode(array $data);

    /**
     * @param $raw
     * @return mixed
     * @throws Exception\TransformationException
     */
    public function decode($raw);
}
