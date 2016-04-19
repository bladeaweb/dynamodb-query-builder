<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/18/16
 * Time: 7:03 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder;

/**
 * Interface QueryBuilderInterface
 *
 * @package Lbstr\DynamoDb\QueryBuilder
 */
interface QueryBuilderInterface {

    /**
     * @return array
     */
    function getQuery();
}