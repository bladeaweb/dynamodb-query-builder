<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 12:50 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class ExpressionCollection
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class ExpressionCollection implements ExpressionInterface {

    /**
     * @var array
     */
    protected $expressions = [];

    /**
     * @var string
     */
    protected $operator;

    /**
     * @param ExpressionInterface $expression
     */
    function addExpression(ExpressionInterface $expression) {

        $this->expressions[] = $expression;
    }

    /**
     * @param array $expressions
     */
    function addExpressionArray(array $expressions) {

        foreach ($expressions as $expression) {
            $this->addExpression($expression);
        }
    }

    /**
     * @return mixed
     */
    function getExpressionString() {

        $expressionStr = '(%s)';

        $expressions = [];
        /** @var ExpressionInterface $expression */
        foreach ($this->expressions as $expression) {
            if (count($expressions)) {
                $expressions[] = $expression->getOperator();
            }
            $expressions[] = $expression->getExpressionString();
        }

        return sprintf($expressionStr, implode(' ', $expressions));
    }

    /**
     * @return array
     */
    function getValue() {

        $value = [];
        /** @var ExpressionInterface $expression */
        foreach ($this->expressions as $expression) {
            $value += $expression->getValue();
        }

        return $value;
    }

    /**
     * @return string
     */
    function getOperator() {

        return $this->operator;
    }

    /**
     * @param string $operator
     */
    function setOperator($operator = 'and') {

        $this->operator = $operator;
    }
}