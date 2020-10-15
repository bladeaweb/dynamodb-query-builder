<?php

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class LtEqExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class LtEqExpression extends ScalarArgExpression {

    /**
     * @var string
     */
    protected $expression = '%s <= :%s';
}