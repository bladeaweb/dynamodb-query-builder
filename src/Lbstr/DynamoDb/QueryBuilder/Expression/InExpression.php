<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 12:23 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

/**
 * Class InExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
class InExpression extends ArrayArgExpression {

    /**
     * @var string
     */
    protected $expression = '%s in (%s)';

    /**
     * @return string
     */
    function getExpressionString() {

        return sprintf(
            $this->expression, $this->key,
            implode(
                ',', array_keys($this->value)
            )
        );
    }
}