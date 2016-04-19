<?php
/**
 * Created by PhpStorm.
 * User: leonidbobylev
 * Date: 4/18/16
 * Time: 5:29 PM
 */

/**
 * Class QueryBuilderTest
 */
class QueryBuilderTest extends DynamoDbTestCase {

    /**
     *
     */
    function testBatchPut() {

        if ($this->tableExist('FooBar')) {
            $this->deleteTable('FooBar');
        }
        $this->createSchema('FooBar');

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->put(
                'FooBar',
                [
                    'id'   => 1,
                    'name' => 'foo'
                ]
            )->put(
                'FooBar',
                [
                    'id'   => 2,
                    'name' => 'baz'
                ]
            )->getQuery();

        $this->db()->batchWriteItem($q);

        $result = $this->scanTable('FooBar');

        $this->assertTrue(count($result) === 2);
        $this->assertEquals($result[1]['name'], 'foo');
        $this->assertEquals($result[1]['id'], 1);
        $this->assertEquals($result[0]['name'], 'baz');
        $this->assertEquals($result[0]['id'], 2);

        $this->deleteTable('FooBar');
    }

    /**
     *
     */
    function testButchDelete() {

        if ($this->tableExist('FooBar')) {
            $this->deleteTable('FooBar');
        }

        $this->createSchema('FooBar');

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->put(
                'FooBar',
                [
                    'id'   => 1,
                    'name' => 'foo'
                ]
            )->put(
                'FooBar',
                [
                    'id'   => 2,
                    'name' => 'baz'
                ]
            )->getQuery();

        $this->db()->batchWriteItem($q);

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->delete('FooBar', ['id' => 1])
            ->delete('FooBar', ['id' => 2])
            ->getQuery();

        $this->db()->batchWriteItem($q);

        $result = $this->scanTable('FooBar');

        $this->assertEquals(count($result), 0);

        $this->deleteTable('FooBar');

    }

    /**
     *
     */
    function testBatchPutDelete() {

        if ($this->tableExist('FooBar')) {
            $this->deleteTable('FooBar');
        }

        $this->createSchema('FooBar');

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->put(
                'FooBar',
                [
                    'id'   => 1,
                    'name' => 'foo'
                ]
            )->put(
                'FooBar',
                [
                    'id'   => 2,
                    'name' => 'baz'
                ]
            )->getQuery();

        $this->db()->batchWriteItem($q);

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->delete('FooBar', ['id' => 1])
            ->delete('FooBar', ['id' => 2])
            ->put(
                'FooBar',
                [
                    'id'   => 3,
                    'name' => 'baz'
                ]
            )->put(
                'FooBar',
                [
                    'id'   => 4,
                    'name' => 'lorem'
                ]
            )
            ->getQuery();

        $this->db()->batchWriteItem($q);

        $result = $this->scanTable('FooBar');

        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]['name'], 'baz');
        $this->assertEquals($result[0]['id'], 3);
        $this->assertEquals($result[1]['name'], 'lorem');
        $this->assertEquals($result[1]['id'], 4);

        $this->deleteTable('FooBar');
    }

    /**
     *
     */
    function testScanAndEq() {

        $tableName = 'FooBar';
        $this->createAndPopulateTable($tableName);

        $q = $this->getQueryBuilder()
            ->scan($tableName)
            ->andEq('id', 256)
            ->getQuery();

        $result = $this->db()->scan($q)['Items'];

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0]['id'], ['N' => 256]);

        $this->deleteTable($tableName);
    }

    /**
     *
     */
    function testScanOrEq() {

        $tableName = 'FooBar';
        $this->createAndPopulateTable($tableName);

        $q = $this->getQueryBuilder()
            ->scan($tableName)
            ->andEq('id', 256)
            ->orEq('id', 643)
            ->getQuery();

        $result = $this->db()->scan($q)['Items'];

        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]['id'], ['N' => 643]);
        $this->assertEquals($result[1]['id'], ['N' => 256]);

        $this->deleteTable($tableName);
    }

    /**
     *
     */
    function testScanContains() {

        $tableName = 'FooBar';
        $this->createAndPopulateTable($tableName);

        $q = $this->getQueryBuilder()
            ->scan($tableName)
            ->withAttributeNames(
                [
                    '#name' => 'name'
                ]
            )
            ->andContains('#name', 'oo')
            ->orContains('#name', 'az')
            ->getQuery();

        $result = $this->db()->scan($q)['Items'];

        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]['name'], ['S' => 'baz']);
        $this->assertEquals($result[1]['name'], ['S' => 'foo']);

        $this->deleteTable($tableName);
    }

    /**
     *
     */
    function testScanBeginsWith() {

        $tableName = 'FooBar';
        $this->createAndPopulateTable($tableName);

        $q = $this->getQueryBuilder()
            ->scan($tableName)
            ->withAttributeNames(
                [
                    '#name' => 'name'
                ]
            )
            ->andBeginsWith('#name', 'f')
            ->orBeginsWith('#name', 'l')
            ->getQuery();

        $result = $this->db()->scan($q)['Items'];

        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]['name'], ['S' => 'lorem']);
        $this->assertEquals($result[1]['name'], ['S' => 'foo']);

        $this->deleteTable($tableName);
    }

    /**
     *
     */
    function testScanIn() {

        $tableName = 'FooBar';
        $this->createAndPopulateTable($tableName);

        $q = $this->getQueryBuilder()
            ->scan($tableName)
            ->withAttributeNames(['#name' => 'name'])
            ->in('#name', ['foo', 'bat'])
            ->orIn('id', [256])
            ->getQuery();

        $result = $this->db()->scan($q)['Items'];

        $this->assertEquals(count($result), 3);
        $this->assertEquals($result[0]['name'], ['S' => 'bat']);
        $this->assertEquals($result[1]['name'], ['S' => 'baz']);
        $this->assertEquals($result[2]['name'], ['S' => 'foo']);

        $this->deleteTable($tableName);
    }

    /**
     * @param $tableName
     */
    function createAndPopulateTable($tableName) {

        if ($this->tableExist($tableName)) {
            $this->deleteTable($tableName);
        }

        $this->createSchema($tableName);

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->put(
                'FooBar',
                [
                    'id'   => 185,
                    'name' => 'foo'
                ]
            )->put(
                'FooBar',
                [
                    'id'   => 256,
                    'name' => 'baz'
                ]
            )->put(
                'FooBar',
                [
                    'id'   => 643,
                    'name' => 'bat'
                ]
            )
            ->put(
                'FooBar',
                [
                    'id'   => 877,
                    'name' => 'lorem'
                ]
            )
            ->getQuery();

        $this->db()->batchWriteItem($q);
    }

}