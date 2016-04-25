<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/25/16
 * Time: 11:23 AM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

use Aws\DynamoDb\Marshaler;
use Lbstr\DynamoDb\QueryBuilder\QueryBuilderException;

/**
 * Class Factory
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class Factory {

    /**
     * @var Marshaler
     */
    protected $marshaler;

    /**
     * Factory constructor.
     *
     * @param Marshaler $marshaler
     */
    function __construct(Marshaler $marshaler) {

        $this->marshaler = $marshaler;
    }

    /**
     * @param array $spec
     *
     * @return AbstractExpression|string
     * @throws QueryBuilderException
     */
    function getExpression(array $spec) {

        extract($spec);
        /** @var string $expression */
        /** @var string $key */
        /** @var mixed $value */
        /** @var string $operator */

        $class = __NAMESPACE__ . '\\' . $expression . 'Expression';
        if (!class_exists($class)) {
            throw QueryBuilderException::expressionNotFound($expression);
        }

        $reflection = new \ReflectionClass($class);
        /** @var AbstractExpression $expression */
        $expression = $reflection->newInstance($this->marshaler)
            ->setKey($key)
            ->setValue($value)
            ->setOperator($operator);

        return $expression;
    }
}