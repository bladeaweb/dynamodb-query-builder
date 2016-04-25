<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 11:55 AM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class NotEqExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class NotEqExpression extends ScalarArgExpression {

    /**
     * @var string
     */
    protected $expression = '%s <> :%s';
}