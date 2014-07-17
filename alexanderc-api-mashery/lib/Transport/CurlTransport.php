<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery\Transport;


use AlexanderC\Api\Mashery\Transport\Exception\TransportException;

class CurlTransport extends AbstractTransport
{
    /**
     * {@inheritdoc}
     */
    protected function __request($url, array $headers)
    {
        $curlHandler = curl_init();

        // CUSTOMREQUEST used to bypass automatic
        // application/x-www-form-urlencoded content type.
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => $headers
        );

        curl_setopt_array($curlHandler, $options);

        if(false === ($response = curl_exec($curlHandler))) {
            throw new TransportException("An error occurred while executing the request: " . curl_error($curlHandler));
        }

        curl_close($curlHandler);

        return $response;
    }

} 