<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/19/16
 * Time: 4:59 AM
 */

namespace Lbstr\DynamoDb\QueryBuilder;

use Aws\DynamoDb\Marshaler;

/**
 * Class Scan
 *
 * @package Lbstr\DynamoDb\QueryBuilder
 */
class Scan extends AbstractQueryBuilder {

    const EXPRESSION_EQ = 'eq';
    const EXPRESSION_CONTAINS = 'contains';
    const EXPRESSION_BEGINS_WITH = 'beginsWith';
    const EXPRESSION_IN = 'in';

    const OPERATOR_AND = 'and';
    const OPERATOR_OR = 'or';

    /**
     *
     * @var array
     */
    protected $filterExpression = [];

    /**
     *
     * @var array
     */
    protected $expressionAttributeValues = [];

    /**
     * @var array
     */
    protected $expressionAttributeNames = [];

    /**
     * @var string
     */
    protected $tableName;

    /**
     * Scan constructor.
     *
     * @param Marshaler $marshaler
     * @param string    $tableName
     */
    function __construct(Marshaler $marshaler, $tableName) {

        parent::__construct($marshaler);
        $this->tableName = $tableName;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    function withAttributeNames(array $attributes) {

        $this->expressionAttributeNames = $attributes;

        return $this;
    }

    protected function addFilterExpression($expression, $key, $value, $operator = self::OPERATOR_AND
    ) {

        if (count($this->filterExpression)) {
            $this->filterExpression[] = $operator;
        }
        $this->filterExpression[] = sprintf(
            $this->expression($expression), $key, $this->paramNum()
        );
        $this->expressionAttributeValues += $this->marshaler->marshalItem($value);

        return $this;
    }

    /**
     * @return int
     */
    protected function paramNum() {

        return count($this->expressionAttributeValues);
    }

    /**
     * @param $expression
     *
     * @return string
     * @throws QueryBuilderException
     */
    protected function expression($expression) {

        switch ($expression) {
            case self::EXPRESSION_EQ:
                return '%s = :p%d';
            case self::EXPRESSION_CONTAINS:
                return 'contains(%s, :p%d)';
            case self::EXPRESSION_BEGINS_WITH:
                return 'begins_with(%s, :p%d)';
            case self::EXPRESSION_IN:
                return '%s in (%s)';
        }

        throw QueryBuilderException::invalidExpression();
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $operator
     *
     * @return Scan
     */
    function eq($key, $value, $operator = self::OPERATOR_AND) {

        return $this->addFilterExpression(
            self::EXPRESSION_EQ, $key, [':p' . $this->paramNum() => $value], $operator
        );
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Scan
     */
    function andEq($key, $value) {

        return $this->eq($key, $value);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Scan
     */
    function orEq($key, $value) {

        return $this->eq($key, $value, self::OPERATOR_OR);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $operator
     *
     * @return Scan
     */
    function contains($key, $value, $operator = self::OPERATOR_AND) {

        return $this->addFilterExpression(
            self::EXPRESSION_CONTAINS, $key, [':p' . $this->paramNum() => $value], $operator
        );
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Scan
     */
    function andContains($key, $value) {

        return $this->contains($key, $value);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Scan
     */
    function orContains($key, $value) {

        return $this->contains($key, $value, self::OPERATOR_OR);
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $operator
     *
     * @return Scan
     */
    function beginsWith($key, $value, $operator = 'and') {

        return $this->addFilterExpression(
            self::EXPRESSION_BEGINS_WITH, $key, [':p' . $this->paramNum() => $value], $operator
        );
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Scan
     */
    function andBeginsWith($key, $value) {

        return $this->beginsWith($key, $value);
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return Scan
     */
    function orBeginsWith($key, $value) {

        return $this->beginsWith($key, $value, self::OPERATOR_OR);
    }

    /**
     * @param string $key
     * @param array  $values
     * @param string $operator
     *
     * @return $this
     */
    function in($key, array $values, $operator = self::OPERATOR_AND) {

        if (!count($values)) {
            return;
        }
        if (count($this->filterExpression)) {
            $this->filterExpression[] = $operator;
        }

        $params = $expressionAttributeValues = [];
        $values = array_unique($values);
        for ($i = 0; $i < count($values); $i++) {
            $params[] = sprintf(':p%d', $this->paramNum());
            $this->expressionAttributeValues += $this->marshaler->marshalItem(
                [
                    $params[$i] => $values[$i]
                ]
            );
        }
        $this->filterExpression[] = sprintf(
            $this->expression(self::EXPRESSION_IN), $key, implode(', ', $params)
        );

        return $this;
    }

    /**
     * @param string $key
     * @param array  $values
     *
     * @return Scan
     */
    function andIn($key, array $values) {

        return $this->in($key, $values);
    }

    /**
     * @param string $key
     * @param array  $values
     *
     * @return Scan
     */
    function orIn($key, array $values) {

        return $this->in($key, $values, self::OPERATOR_OR);
    }

    /**
     * @return array
     */
    function getQuery() {

        if (!count($this->filterExpression) || !count($this->expressionAttributeValues)) {
            return [];
        }

        $query = [
            'TableName'                 => $this->tableName,
            'FilterExpression'          => implode(' ', $this->filterExpression),
            'ExpressionAttributeValues' => $this->expressionAttributeValues
        ];

        if (count($this->expressionAttributeNames)) {
            $query += [
                'ExpressionAttributeNames' => $this->expressionAttributeNames
            ];
        }

        return $query;
    }
}