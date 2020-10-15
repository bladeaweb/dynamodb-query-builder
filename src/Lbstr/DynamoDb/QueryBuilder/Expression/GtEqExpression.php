<?php

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class GtEqExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class GtEqExpression extends ScalarArgExpression {

    /**
     * @var string
     */
    protected $expression = '%s >= :%s';
}