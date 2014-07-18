<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery\Transport;


use AlexanderC\Api\Mashery\Transport\Exception\AuthorizationException;
use AlexanderC\Api\Mashery\Transport\Exception\TransportException;

class CurlTransport extends AbstractTransport
{
    /**
     * {@inheritdoc}
     */
    protected function __request($url, array $headers, &$authorizationError)
    {
        $authorizationError = false;
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
        $response = curl_exec($curlHandler);
        $statusCode = false === $response ? null : curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        curl_close($curlHandler);

        if (false === $response) {
            throw new TransportException("An error occurred while executing the request: " . curl_error($curlHandler));
        } elseif(403 === $statusCode) {
            $authorizationError = true;
        }

        return $response;
    }

} 