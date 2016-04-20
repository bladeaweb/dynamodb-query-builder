<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 11:53 AM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Interface ExpressionInterface
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
interface ExpressionInterface {

    /**
     * @return string
     */
    function getExpressionString();

    /**
     * @return mixed
     */
    function getValue();

    /**
     * @return string
     */
    function getOperator();
}