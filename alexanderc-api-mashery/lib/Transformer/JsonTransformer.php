<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery\Transformer;


use AlexanderC\Api\Mashery\Transformer\Exception\TransformationException;

class JsonTransformer implements TransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function encode(array $data)
    {
        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function decode($raw)
    {
        $data = @json_decode($raw, true);

        if (null === $data) {
            throw new TransformationException("Data can not be decoded: " . json_last_error());
        }

        return $data;
    }

} 