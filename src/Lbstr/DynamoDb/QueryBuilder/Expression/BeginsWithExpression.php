<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 12:22 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class BeginsWithExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class BeginsWithExpression extends ScalarArgExpression {

    /**
     * @var string
     */
    protected $expression = 'begins_with(%s, :%s)';
}