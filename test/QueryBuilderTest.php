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

    protected $tableName = 'FooBar';

    protected $fixture
        = [
            [
                'id'     => 185,
                'name'   => 'foo',
                'status' => 'alert'
            ],
            [
                'id'     => 256,
                'name'   => 'baz',
                'status' => 'success'
            ],
            [
                'id'     => 643,
                'name'   => 'bat',
                'status' => 'stopped'
            ],

        ];

    function setUp() {

        if ($this->tableExist($this->tableName)) {
            $this->deleteTable($this->tableName);
        }
        $this->createSchema($this->tableName);

        $qb = $this->getQueryBuilder()->batchWriteItem();
        foreach ($this->fixture as $item) {
            $qb->put($this->tableName, $item);
        }
        $q = $qb->getQuery();

        $this->db()->batchWriteItem($q);
    }

    function tearDown() {

        if ($this->tableExist($this->tableName)) {
            $this->deleteTable($this->tableName);
        }
    }

    function testBatchPut() {

        $result = $this->scanTable($this->tableName);

        $this->assertTrue(count($result) === count($this->fixture));
        $this->assertEquals($result[1]['name'], 'baz');
        $this->assertEquals($result[1]['id'], 256);
        $this->assertEquals($result[0]['name'], 'bat');
        $this->assertEquals($result[0]['id'], 643);
    }

    function testButchDelete() {

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->delete($this->tableName, ['id' => 256])
            ->delete($this->tableName, ['id' => 643])
            ->delete($this->tableName, ['id' => 185])
            ->getQuery();
        $this->db()->batchWriteItem($q);
        $result = $this->scanTable($this->tableName);

        $this->assertEquals(count($result), 0);

    }

    function testBatchPutDelete() {

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->delete($this->tableName, ['id' => 256])
            ->delete($this->tableName, ['id' => 643])
            ->delete($this->tableName, ['id' => 185])
            ->put(
                $this->tableName,
                [
                    'id'   => 3,
                    'name' => 'baz'
                ]
            )
            ->put(
                $this->tableName,
                [
                    'id'   => 4,
                    'name' => 'lorem'
                ]
            )
            ->getQuery();

        $this->db()->batchWriteItem($q);
        $result = $this->scanTable($this->tableName);

        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]['name'], 'baz');
        $this->assertEquals($result[0]['id'], 3);
        $this->assertEquals($result[1]['name'], 'lorem');
        $this->assertEquals($result[1]['id'], 4);
    }


    function testScanAndEq() {

        $q = $this->getQueryBuilder()
            ->scan($this->tableName)
            ->andEq('id', 256)
            ->getQuery();

        $result = $this->db()->scan($q)['Items'];

        $this->assertEquals(count($result), 1);
        $this->assertEquals($result[0]['id'], ['N' => 256]);

        $this->deleteTable($this->tableName);
    }


    function testScanOrEq() {

        $q = $this->getQueryBuilder()
            ->scan($this->tableName)
            ->andEq('id', 256)
            ->orEq('id', 643)
            ->getQuery();

        $result = $this->db()->scan($q)['Items'];

        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]['id'], ['N' => 643]);
        $this->assertEquals($result[1]['id'], ['N' => 256]);
    }


    function testScanContains() {

        $q = $this->getQueryBuilder()
            ->scan($this->tableName)
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
    }


    function testScanBeginsWith() {

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->put(
                $this->tableName,
                [
                    'id'   => 4,
                    'name' => 'lorem'
                ]
            )
            ->getQuery();
        $this->db()->batchWriteItem($q);

        $q = $this->getQueryBuilder()
            ->scan($this->tableName)
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
        $this->assertEquals($result[0]['name'], ['S' => 'foo']);
        $this->assertEquals($result[1]['name'], ['S' => 'lorem']);
    }

    function testScanIn() {

        $q = $this->getQueryBuilder()
            ->scan($this->tableName)
            ->withAttributeNames(['#name' => 'name'])
            ->in('#name', ['foo', 'bat'])
            ->orIn('id', [256])
            ->getQuery();

        $result = $this->db()->scan($q)['Items'];

        $this->assertEquals(count($result), 3);
        $this->assertEquals($result[0]['name'], ['S' => 'bat']);
        $this->assertEquals($result[1]['name'], ['S' => 'baz']);
        $this->assertEquals($result[2]['name'], ['S' => 'foo']);
    }

    function testSubQuery() {

        $q = $this->getQueryBuilder()->batchWriteItem()
            ->put(
                $this->tableName,
                [
                    'id'     => 33434,
                    'name'   => 'foobar',
                    'status' => 'error'
                ]
            )
            ->put(
                $this->tableName,
                [
                    'id'     => 4343,
                    'name'   => 'bazbat',
                    'status' => 'success'
                ]
            )
            ->put(
                $this->tableName,
                [
                    'id'     => 3312,
                    'name'   => 'jazz',
                    'status' => 'error'
                ]
            )
            ->put(
                $this->tableName,
                [
                    'id'     => 12312,
                    'name'   => 'blues',
                    'status' => 'error'
                ]
            )
            ->getQuery();

        $this->db()->batchWriteItem($q);

        $qb = $this->getQueryBuilder()
            ->scan($this->tableName)
            ->withAttributeNames(['#name' => 'name', '#status' => 'status'])
            ->eq('#status', 'error')
            ->subQuery(
                $this->getQueryBuilder()
                    ->scan($this->tableName)
                    ->withAttributeNames(['#name' => 'name'])
                    ->beginsWith('#name', 'ja')
                    ->orBeginsWith('#name', 'fo')
            );

        $items = $this->db()->scan($qb->getQuery())['Items'];

        $this->assertEquals(count($items), 2);
        foreach ($items as $item) {
            $item = $this->getMarshaler()->unmarshalItem($item);
            $this->assertEquals($item['status'], 'error');
            $this->assertEquals(1, preg_match('/^(ja|fo).*$/', $item['name']));
        }
    }

}