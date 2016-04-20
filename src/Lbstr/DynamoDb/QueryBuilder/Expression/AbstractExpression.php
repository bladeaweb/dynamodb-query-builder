<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 11:52 AM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

use Aws\DynamoDb\Marshaler;

/**
 * Class AbstractExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
abstract class AbstractExpression implements ExpressionInterface {

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var Marshaler
     */
    protected $marshaler;

    /**
     * AbstractExpression constructor.
     *
     * @param Marshaler $marshaler
     */
    function __construct(Marshaler $marshaler) {

        $this->marshaler = $marshaler;
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    function setKey($key) {

        $this->key = $key;

        return $this;
    }

    /**
     * @param string $operator
     *
     * @return $this
     */
    function setOperator($operator) {

        $this->operator = $operator;

        return $this;
    }

    /**
     * @return string
     */
    function getOperator() {

        return $this->operator;
    }
}