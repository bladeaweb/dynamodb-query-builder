<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/18/16
 * Time: 5:27 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder;

use Aws\DynamoDb\Marshaler;

/**
 * Class QueryBuilder
 *
 * @package Lbstr\DynamoDb\QueryBuilder
 */
class QueryBuilder extends AbstractQueryBuilder {

    /**
     * @return BatchWriteItem
     */
    function batchWriteItem() {

        return new BatchWriteItem($this->marshaler);
    }

    /**
     * @param string $tableName
     *
     * @return Scan
     */
    function scan($tableName) {

        return new Scan($this->marshaler, $tableName);
    }

}