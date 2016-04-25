<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/19/16
 * Time: 6:16 AM
 */

namespace Lbstr\DynamoDb\QueryBuilder;

/**
 * Class QueryBuilderException
 *
 * @package Lbstr\DynamoDb\QueryBuilder
 */
class QueryBuilderException extends \Exception {

    /**
     * @param string          $message
     * @param int             $code
     * @param \Exception|null $previous
     *
     * @return QueryBuilderException
     */
    static function invalidExpression($message = 'Invalid filter expression', $code = 0,
        \Exception $previous = null
    ) {

        return new self($message, $code, $previous);
    }

    /**
     * @param string $expression
     *
     * @return QueryBuilderException
     */
    static function expressionNotFound($expression) {

        return new self(
            sprintf(
                'Expression "%s" not found', htmlspecialchars($expression)
            )
        );
    }
}