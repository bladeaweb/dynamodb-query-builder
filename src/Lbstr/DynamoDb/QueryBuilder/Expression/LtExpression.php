<?php

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class LtExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class LtExpression extends ScalarArgExpression {

    /**
     * @var string
     */
    protected $expression = '%s < :%s';
}