<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;


class QueryResponse extends Response
{
    /**
     * @return QueryResult
     */
    public function getResult()
    {
        return new QueryResult($this->result);
    }
} 