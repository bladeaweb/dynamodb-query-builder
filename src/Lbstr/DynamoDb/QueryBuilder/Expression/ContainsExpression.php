<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 12:07 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class ContainsExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class ContainsExpression extends ScalarArgExpression {

    /**
     * @var string
     */
    protected $expression = 'contains(%s, :%s)';
}