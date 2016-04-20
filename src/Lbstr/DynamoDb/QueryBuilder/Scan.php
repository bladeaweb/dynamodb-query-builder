<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/19/16
 * Time: 4:59 AM
 */

namespace Lbstr\DynamoDb\QueryBuilder;

use Aws\DynamoDb\Marshaler;
use Lbstr\DynamoDb\QueryBuilder\Expression\BeginsWithExpression;
use Lbstr\DynamoDb\QueryBuilder\Expression\ContainsExpression;
use Lbstr\DynamoDb\QueryBuilder\Expression\EqExpression;
use Lbstr\DynamoDb\QueryBuilder\Expression\ExpressionCollection;
use Lbstr\DynamoDb\QueryBuilder\Expression\GenericExpression;
use Lbstr\DynamoDb\QueryBuilder\Expression\InExpression;

/**
 * Class Scan
 *
 * @package Lbstr\DynamoDb\QueryBuilder
 */
class Scan extends AbstractQueryBuilder {

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
     * @var ExpressionCollection
     */
    protected $expressions;

    /**
     * Scan constructor.
     *
     * @param Marshaler $marshaler
     * @param string    $tableName
     */
    function __construct(Marshaler $marshaler, $tableName) {

        parent::__construct($marshaler);
        $this->tableName = $tableName;
        $this->expressions = new ExpressionCollection();
    }

    function getExpressions() {

        return $this->expressions;
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

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $operator
     *
     * @return Scan
     */
    function eq($key, $value, $operator = self::OPERATOR_AND) {

        $expression = new EqExpression($this->marshaler);
        $expression->setKey($key)
            ->setValue($value)
            ->setOperator($operator);

        $this->expressions->addExpression($expression);

        return $this;
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

        $expression = new ContainsExpression($this->marshaler);
        $expression->setKey($key)
            ->setValue($value)
            ->setOperator($operator);

        $this->expressions->addExpression($expression);

        return $this;
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

        $expression = new BeginsWithExpression($this->marshaler);
        $expression->setKey($key)
            ->setValue($value)
            ->setOperator($operator);

        $this->expressions->addExpression($expression);

        return $this;
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

        $expression = new InExpression($this->marshaler);
        $expression->setKey($key)
            ->setValue($values)
            ->setOperator($operator);

        $this->expressions->addExpression($expression);

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
     * @param Scan   $qb
     * @param string $operator
     *
     * @return $this
     */
    function subQuery(Scan $qb, $operator = 'and') {

        $this->expressions->addExpression(
            new GenericExpression(
                $qb->getExpressions()->getExpressionString(),
                $qb->getExpressions()->getValue(),
                $operator
            )
        );

        return $this;
    }

    /**
     * @return array
     */
    function getQuery() {

        $query = [
            'TableName'                 => $this->tableName,
            'FilterExpression'          => $this->expressions->getExpressionString(),
            'ExpressionAttributeValues' => $this->expressions->getValue()
        ];

        if (count($this->expressionAttributeNames)) {
            $query += [
                'ExpressionAttributeNames' => $this->expressionAttributeNames
            ];
        }

        return $query;
    }
}