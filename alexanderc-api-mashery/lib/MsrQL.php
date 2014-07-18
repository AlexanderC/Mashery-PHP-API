<?php

/**
 * @author AlexanderC <self@alexanderc.me>
 * @package MasheryApi
 */

namespace AlexanderC\Api\Mashery;


class MsrQL
{
    const DEFAULT_SELECTOR = '*';
    const SELECT = 'SELECT';

    /**
     * @var string
     */
    protected $expressionRelationRegex = '(?:(?:AND)|(:?OR))';

    /**
     * @var string
     */
    protected $operandRegex = '(?:(?:NOT)|<|>|=|(?:<\s*>)|(?:<\s*=)|(?:>\s*=)|(?:LIKE)|(?:NOT\s+LIKE))';

    /**
     * @var string
     */
    protected $expressionNamedComparatorRegex = '(?:(?:IS\s+NULL)|(?:IS\s+NOT\s+NULL))';

    /**
     * @var string
     */
    protected $expressionPartLeftRegex = '(?:[\w]+)';

    /**
     * @var string
     */
    protected $expressionPartRightRegex = '(?:(?:(?<rpenc%num%>["\'])(?:\w+)?\k<rpenc%num%>)|(:?TRUE)|(:?FALSE)|(?:NULL)|(:?\d+))';

    /**
     * Types: SELECT (would be probably added in newer versions)
     *
     * @var string
     */
    protected $type = self::SELECT;

    /**
     * @var string
     */
    protected $selector = self::DEFAULT_SELECTOR;

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $where;

    /**
     * @var string
     */
    protected $orderBy;

    /**
     * @var
     */
    protected $orderType;

    /**
     * @var int
     */
    protected $items;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var string
     */
    protected $requireRelatedTable;

    /**
     * @var string
     */
    protected $requireRelatedExpression;

    /**
     * @return $this
     */
    public static function create()
    {
        return new self();
    }

    /**
     * @param string $selector
     * @return $this
     */
    public function select($selector)
    {
        $this->type = self::SELECT;
        $this->selector = $selector;

        return $this;
    }

    /**
     * @param string $object
     * @return $this
     */
    public function from($object)
    {
        $this->table = $object;

        return $this;
    }

    /**
     * @param string $expression
     * @return $this
     */
    public function where($expression)
    {
        if(!empty($this->where)) {
            $this->andWhere($expression);
        }

        $this->cleanQueryExpression($expression);

        $this->validateConditionalExpression($expression);

        $this->where = sprintf('WHERE (%s)', $expression);

        return $this;
    }

    /**
     * @param string $expression
     * @return $this
     */
    public function andWhere($expression)
    {
        if(empty($this->where)) {
            $this->where($expression);
        }

        $this->cleanQueryExpression($expression);

        $this->validateConditionalExpression($expression);

        $this->where .= sprintf(' AND (%s)', $expression);

        return $this;
    }

    /**
     * @param string $expression
     * @return $this
     */
    public function orWhere($expression)
    {
        if(empty($this->where)) {
            $this->where($expression);
        }

        $this->cleanQueryExpression($expression);

        $this->validateConditionalExpression($expression);

        $this->where .= sprintf(' OR (%s)', $expression);

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function descendingOrderBy($field)
    {
        $this->orderBy = $field;
        $this->orderType = 'DESC';

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function ascendingOrderBy($field)
    {
        $this->orderBy = $field;
        $this->orderType = 'ASC';

        return $this;
    }

    /**
     * @param int $count
     * @return $this
     */
    public function items($count)
    {
        $this->items = (int) $count;
        $this->items = $this->items < 1 ? 1 : $this->items;

        return $this;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function page($number)
    {
        $this->page = (int) $number;
        // TODO: figure out what's the first page starting from
        $this->page = $this->page < 1 ? 1 : $this->page;

        return $this;
    }

    /**
     * @param string $object
     * @param string $expression
     * @return $this
     */
    public function requireRelated($object, $expression)
    {
        $this->cleanQueryExpression($expression);

        $this->validateConditionalExpression($expression);

        $this->requireRelatedTable = $object;
        $this->requireRelatedExpression = $expression;

        return $this;
    }

    /**
     * @param string $expression
     * @throws \RuntimeException
     */
    protected function validateConditionalExpression($expression)
    {
        if(preg_match("#[\(\)]#ui", $expression)) {
            throw new \RuntimeException("You should not use grouping parentheses here");
        }

        $expressionRegex = sprintf(
            "\s*((%s)\s*(:?(?:(%s)\s*(%s))|(%s)))\s*",
            $this->expressionPartLeftRegex,
            $this->operandRegex,
            $this->expressionPartRightRegex,
            $this->expressionNamedComparatorRegex
        );

        $regex = sprintf(
            "#^%s(:?%s\s+%s)*$#ui",
            str_replace("%num%", 1, $expressionRegex),
            $this->expressionRelationRegex,
            str_replace("%num%", 2, $expressionRegex)
        );

        if(!preg_match($regex, $expression, $matches)) {
            throw new \RuntimeException("`{$expression}` is not a valid conditional expression");
        }
    }

    /**
     * @param string $expression
     * @return mixed|string
     */
    protected function cleanQueryExpression(&$expression)
    {
        $expression = is_string($expression) ? trim(preg_replace("/\s+/u", " ", $expression)) : "";
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if(empty($this->table)) {
            throw new \RuntimeException("You should specify object before querying");
        }

        $query = "{$this->type} {$this->selector} FROM {$this->table} {$this->where}";

        if(!empty($this->requireRelatedTable)) {
            $query .= " REQUIRE RELATED {$this->requireRelatedTable} WITH {$this->requireRelatedExpression}";
        }

        if(!empty($this->orderBy)) {
            $query .= " ORDER BY {$this->orderBy} {$this->orderType}";
        }

        if(!empty($this->page)) {
            $query .= " PAGE {$this->page}";
        }

        if(!empty($this->items)) {
            $query .= " ITEMS {$this->items}";
        }

        return $query;
    }
} 