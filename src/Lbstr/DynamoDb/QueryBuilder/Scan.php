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
use Lbstr\DynamoDb\QueryBuilder\Expression\ExpressionCollection;
use Lbstr\DynamoDb\QueryBuilder\Expression\Factory;
use Lbstr\DynamoDb\QueryBuilder\Expression\GenericExpression;
use Lbstr\DynamoDb\QueryBuilder\Expression\InExpression;

/**
 * Class Scan
 *
 * @method Scan andNotEq(string $key, string $value) Add "Or Not Equal" condition to query.
 * @method Scan andEq(string $key, string $value) Add "And Equal" condition to query.
 * @method Scan orEq(string $key, string $value) Add "Or Equal" condition to query.
 * @method Scan andContains(string $key, string $value) Add "And Contains" condition to query.
 * @method Scan orContains(string $key, string $value) Add "Or Contains" condition to query.
 * @method Scan andBeginsWith(string $key, string $value) Add "And Begins With" condition to query.
 * @method Scan orBeginsWith(string $key, string $value) Add "Or Begins With" condition to query.
 * @method Scan andIn(string $key, string $value) Add "And In" condition to query.
 * @method Scan orIn(string $key, string $value) Add "Or In" condition to query.
 *
 * @package Lbstr\DynamoDb\QueryBuilder
 */
class Scan extends AbstractQueryBuilder {

    const OPERATOR_AND = 'and';
    const OPERATOR_OR = 'or';

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
     * @var Factory
     */
    protected $expressionFactory;

    /**
     * Scan constructor.
     *
     * @param Marshaler $marshaler
     * @param string    $tableName
     */
    function __construct(Marshaler $marshaler, $tableName, Factory $expressionFactory = null) {

        parent::__construct($marshaler);
        $this->tableName = $tableName;
        $this->expressions = new ExpressionCollection();
        if ($expressionFactory === null) {
            $expressionFactory = new Factory(
                $marshaler
            );
        }
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    function __call($name, $arguments) {

        if (preg_match('/(or|and)(.*)/', $name, $m)) {

            $arguments[] = $m[1];
            return call_user_func_array(
                [$this, lcfirst($m[2])], $arguments
            );
        }

        throw new \BadMethodCallException(
            sprintf(
                'Bad method call "%s"', htmlspecialchars($name)
            )
        );
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

        $this->expressions->addExpression(
            $this->expressionFactory->getExpression(
                [
                    'expression' => 'Eq',
                    'key'        => $key,
                    'value'      => $value,
                    'operator'   => $operator
                ]
            )
        );

        return $this;
    }

    /**
     * @param string $key
     * @param mixed  $value
     * @param string $operator
     *
     * @return Scan
     */
    function notEq($key, $value, $operator = self::OPERATOR_AND) {

        $this->expressions->addExpression(
            $this->expressionFactory->getExpression(
                [
                    'expression' => 'NotEq',
                    'key'        => $key,
                    'value'      => $value,
                    'operator'   => $operator
                ]
            )
        );

        return $this;
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