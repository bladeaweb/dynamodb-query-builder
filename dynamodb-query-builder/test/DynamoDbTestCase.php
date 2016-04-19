<?php

/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/18/16
 * Time: 8:00 PM
 */
class DynamoDbTestCase extends \PHPUnit_Framework_TestCase {

    /**
     * @return \Aws\DynamoDb\DynamoDbClient
     */
    protected function db() {

        $sdk = new \Aws\Sdk(
            [
                'region'      => 'us-west-2',
                'version'     => 'latest',
                'endpoint'    => 'http://localhost:8000',
                'credentials' => [
                    'key'    => 'x',
                    'secret' => 'y',
                ]
            ]
        );

        return $sdk->createDynamoDb();
    }

    /**
     * @param $tableName
     */
    protected function createSchema($tableName) {

        $db = $this->db();
        $db->createTable(
            [
                'AttributeDefinitions'  =>
                    [
                        [
                            'AttributeName' => 'id', // REQUIRED
                            'AttributeType' => 'N', // REQUIRED
                        ],
                    ],
                'TableName'             => $tableName,
                'KeySchema'             =>
                    [
                        [
                            'AttributeName' => 'id',
                            'KeyType'       => 'HASH'
                        ]
                    ],
                'ProvisionedThroughput' => [
                    'ReadCapacityUnits'  => 1,
                    'WriteCapacityUnits' => 1
                ]
            ]
        );
    }

    /**
     * @param $tableName
     */
    protected function deleteTable($tableName) {

        $db = $this->db();
        $db->deleteTable(
            [
                'TableName' => $tableName
            ]
        );
    }

    /**
     * @return \Aws\DynamoDb\Marshaler
     */
    protected function getMarshaler() {

        return new \Aws\DynamoDb\Marshaler();
    }

    /**
     * @param $tableName
     *
     * @return array
     */
    protected function scanTable($tableName) {

        return array_map(
            function ($item) {
                return $this->getMarshaler()->unmarshalItem($item);
            },
            $this->db()->scan(
                [
                    'TableName' => $tableName
                ]
            )['Items']
        );
    }

    /**
     * @return \Lbstr\DynamoDb\QueryBuilder\QueryBuilder
     */
    protected function getQueryBuilder() {

        return new \Lbstr\DynamoDb\QueryBuilder\QueryBuilder(
            $this->getMarshaler()
        );
    }

    /**
     * @param $tableName
     *
     * @return bool
     */
    protected function tableExist($tableName) {

        $result = $this->db()->listTables()['TableNames'];

        return in_array($tableName, $result);
    }
}