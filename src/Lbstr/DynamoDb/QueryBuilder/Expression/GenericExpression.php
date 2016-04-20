<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 1:02 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class GenericExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class GenericExpression implements ExpressionInterface {

    /**
     * @var string
     */
    protected $expressionStr;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $operator;

    /**
     * GenericExpression constructor.
     *
     * @param string $expressionStr
     * @param mixed  $value
     * @param string $operator
     */
    function __construct($expressionStr, $value, $operator = 'and') {

        $this->expressionStr = $expressionStr;
        $this->value = $value;
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    function getExpressionString() {

        return $this->expressionStr;
    }

    /**
     * @return mixed
     */
    function getValue() {

        return $this->value;
    }

    /**
     * @return string
     */
    function getOperator() {

        return $this->operator;
    }
}