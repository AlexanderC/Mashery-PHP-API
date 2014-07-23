<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;


use AlexanderC\Api\Mashery\Helpers\Inflector;

class QueryResult
{
    use ResponseDataValidatorTrait;

    /**
     * @var int
     */
    protected $totalItems;

    /**
     * @var int
     */
    protected $totalPages;

    /**
     * @var int
     */
    protected $itemsPerPage;

    /**
     * @var int
     */
    protected $currentPage;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var array
     */
    protected $attributes = [
        'total_items', 'total_pages',
        'items_per_page', 'current_page', 'items'
    ];

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->validate($data);

        foreach ($this->attributes as $attribute) {
            $parameter = Inflector::camelize($attribute);

            $this->{$parameter} = $data[$attribute];
        }
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }
} 