<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/18/16
 * Time: 7:02 PM
 */

namespace Lbstr\DynamoDb\QueryBuilder;

/**
 * Class BatchWriteItem
 *
 * @package Lbstr\DynamoDb\QueryBuilder
 */
class BatchWriteItem extends AbstractQueryBuilder {

    /**
     * @var array
     */
    protected $query
        = [
            'RequestItems' => []
        ];

    /**
     * @param string $tableName
     * @param array  $key
     *
     * @return $this
     */
    function delete($tableName, array $key) {

        if (empty($this->query['RequestItems'][$tableName])) {
            $this->query['RequestItems'][$tableName] = [
                [
                    'DeleteRequest' => [
                        'Key' => $this->marshaler->marshalItem($key)
                    ]
                ]
            ];
        } else {
            $this->query['RequestItems'][$tableName][] = [
                'DeleteRequest' => [
                    'Key' => $this->marshaler->marshalItem($key)
                ]
            ];
        }

        return $this;
    }


    /**
     * @param string $tableName
     * @param array  $item
     *
     * @return $this
     */
    function put($tableName, array $item) {

        if (empty($this->query['RequestItems'][$tableName])) {
            $this->query['RequestItems'][$tableName] = [
                [
                    'PutRequest' => [
                        'Item' => $this->marshaler->marshalItem($item)
                    ]
                ]
            ];
        } else {
            $this->query['RequestItems'][$tableName][] = [
                'PutRequest' => [
                    'Item' => $this->marshaler->marshalItem($item)
                ]
            ];
        }

        return $this;
    }
}