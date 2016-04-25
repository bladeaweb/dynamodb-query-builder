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
 * Class ArrayArgExpression
 *
 * @package Lbstr\DynamoDb\QueryBuilder\Expression
 */
abstract class ArrayArgExpression extends AbstractExpression {

    /**
     * @var array
     */
    protected $value = [];

    /**
     * @var array
     */
    protected $paramNames = [];

    /**
     * ArrayArgExpression constructor.
     *
     * @param Marshaler $marshaler
     */
    function __construct(Marshaler $marshaler) {

        parent::__construct($marshaler);
    }

    /**
     * @param array $value
     *
     * @return $this
     * @throws \InvalidArgumentException
     */
    function setValue($value) {

        if(!is_array($value)) {
            throw new \InvalidArgumentException(
                'Argument should be array'
            );
        }

        foreach ($value as $val) {
            $this->value += $this->marshaler->marshalItem(
                [
                    ':' . uniqid() => $val
                ]
            );
        }

        return $this;
    }

    /**
     * @return array
     */
    function getValue() {

        return $this->value;
    }
}