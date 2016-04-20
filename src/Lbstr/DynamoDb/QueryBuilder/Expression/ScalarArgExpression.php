<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/20/16
 * Time: 11:51 AM
 */

namespace Lbstr\DynamoDb\QueryBuilder\Expression;

use Aws\DynamoDb\Marshaler;

/**
 * Class ScalarArgExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
abstract class ScalarArgExpression extends AbstractExpression {

    /**
     * @var array
     */
    protected $value;

    /**
     * @var string
     */
    protected $paramName;

    /**
     * ScalarArgExpression constructor.
     *
     * @param Marshaler $marshaler
     */
    function __construct(Marshaler $marshaler) {

        parent::__construct($marshaler);

        $this->paramName = uniqid();
    }

    /**
     * @param $value
     *
     * @return $this
     */
    function setValue($value) {

        $this->value = $value;

        return $this;
    }

    /**
     * @return array
     */
    function getValue() {

        return $this->marshaler->marshalItem([':' . $this->paramName => $this->value]);
    }

    /**
     * @return string
     */
    function getExpressionString() {

        return sprintf($this->expression, $this->key, $this->paramName);
    }
}