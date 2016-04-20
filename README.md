# dynamodb-query-builder
Amazon DynamoDb query builder.

```php
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
$db = $sdk->createDynamoDb();
$qb = new \Lbstr\DynamoDb\QueryBuilder\QueryBuilder(
    new \Aws\DynamoDb\Marshaler()
);

// batch write
$tableName = 'FooBar';
$q = $qb->batchWriteItem()
        ->put($tableName, ['id' => 1, 'name' => 'foo'])
        ->put($tableName, ['id' => 2, 'name' => 'baz'])
        ->delete($tableName, ['id' => 15])
        ->delete('AnotherTable', ['id' => 32])
        ->put('AnotherTable', ['id' => 32, 'name' => 'bar'])
        ->getQuery();

$db->batchWriteItem($q);

// scan
$q = $qb->scan($tableName)
        ->eq('id', 256)
        ->orEq('id', 643)
        ->orContains('status', 'error')
        ->getQuery();


$q = $qb->scan($tableName)
        ->withAttributeNames(['#name' => 'name'])
        ->in('#name', ['foo', 'bat'])
        ->orIn('id', [256])
        ->getQuery();
      
$q = $qb->scan($tableName)
        ->withAttributeNames(
            [
                '#name' => 'name'
            ]
        )
        ->beginsWith('#name', 'f')
        ->orBeginsWith('#name', 'l')
        ->getQuery();
      
$q = $qb->scan($tableName)
        ->withAttributeNames(
            [
                '#name' => 'name'
            ]
        )
        ->contains('#name', 'oo')
        ->orContains('#name', 'az')
        ->getQuery();
        
// sub-query example
$qb = $this->getQueryBuilder()
    ->scan('MyTable')
    ->withAttributeNames(['#name' => 'name', '#status' => 'status'])
    ->eq('#status', 'error')
    ->subQuery(
        $this->getQueryBuilder()
            ->scan('MyTable')
            ->withAttributeNames(['#name' => 'name'])
            ->beginsWith('#name', 'ja')
            ->orBeginsWith('#name', 'fo')
    );

/*
Array
(
    [TableName] => FooBar
    [FilterExpression] => (#status = :571769ca57e29 and (begins_with(#name, :571769ca57eb9) or begins_with(#name, :571769ca57f26)))
    [ExpressionAttributeValues] => Array
        (
            [:571769ca57e29] => Array
                (
                    [S] => error
                )

            [:571769ca57eb9] => Array
                (
                    [S] => ja
                )

            [:571769ca57f26] => Array
                (
                    [S] => fo
                )

        )

    [ExpressionAttributeNames] => Array
        (
            [#name] => name
            [#status] => status
        )

)*/
    
$items = $this->db()->scan($qb->getQuery())['Items'];
```
