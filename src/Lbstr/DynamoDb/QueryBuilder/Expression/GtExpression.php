<?php

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class GtExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class GtExpression extends ScalarArgExpression {

    /**
     * @var string
     */
    protected $expression = '%s > :%s';
}