<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/18/16
 * Time: 7:04 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder;

use Aws\DynamoDb\Marshaler;

/**
 * Class AbstractQueryBuilder
 *
 * @package Lbstr\DynamoDb\QueryBuilder
 */
abstract class AbstractQueryBuilder implements QueryBuilderInterface {

    /**
     * @var Marshaler
     */
    protected $marshaler;

    /**
     * @var array
     */
    protected $query;

    /**
     * QueryBuilder constructor.
     *
     * @param Marshaler $marshaler
     */
    function __construct(Marshaler $marshaler) {

        $this->marshaler = $marshaler;
    }

    /**
     * @return array
     */
    function getQuery() {

        return $this->query;
    }
}