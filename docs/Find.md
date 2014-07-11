# Find

When you call ``findAll`` on the model (static) or on the repo itself, a new "Find" object is created.
It is used to construct a query to retrieve models. It is tied to a repo so it knows which model class it has to load.

Here are the methods for building the query:

Method                                                     | Description
-----------------------------------------------------------|------------------------------------------------
__column__($column, $alias = null)                         | add a custom column to the list, in addition to the default ``{table name}.*``. You can add more than one custom column by calling the method multiple times. To pass a custom SQL with parameters, you can pass a ``Harp\Query\SQL\SQL`` object
__prependColumn__($column, $alias = null)                  | prepend a custom column to the list
__where__($column, $value)                                 | add an equals condition (=). For all "where" methods multiple calls are added with an "AND"
__whereNot__($column, $value)                              | add a not equals condition (!=).
__whereIn__($column, array $values)                        | add "in array" condition (IN)
__whereLike__($column, $value)                             | add a like condition (LIKE)
__whereKey__($key)                                         | add a condition for a primary key (id = value)
__whereRaw__($sql, array $parameters = array()             | add a custom sql. This is useful to add "or" conditions
__having__($column, $value)                                | add an equals condition (=). For all "where" methods multiple calls are added with an "AND"
__havingNot__($column, $value)                             | add a not equals (!=) to HAVING
__havingIn__($column, array $values)                       | add an "in array" condition (IN) to HAVING
__havingLike__($column, $value)                            | add a like condition (LIKE) to HAVING
__havingRaw__($sql, array $parameters = array()            | add a custom sql. This is useful to add "or" conditions
__group__($column, $direction = null)                      | add a GROUP BY
__order__($column, $direction = null)                      | add an ORDER BY
__join__($table, $conditions, $type = null)                | add a JOIN to another table. $conditions can be either be an array, which will be transformed to a "ON {key} = {value}" or you can pass a custom SQL via string
__joinRels__(array $rels)                                  | join related models if they have a proper configuration. Argument can be a nested array.
__joinAliased__($table, $alias, $conditions, $type = null) | the same as __join__, but the table will have an alias
__limit__($limit)                                          | add limit
__offset__($offset)                                        | add an offset

After you've constructed the query you need to actually execute it and get the result. This is done with the ``load*`` methods.

Method          | Description
----------------|------------------------------------------------
__load__()      | Returns a "Models" object which you can iterate over, pluck properties etc. Read a detailed [description here](#models)
__loadCount__() | Will generate a "count" query and return the result. This is faster than calling ``load()->count()``
__loadIds__()   | Returns array of ids. Generates an efficient query and does not retrieve the whole models
__loadFirst__() | Will add a limit(1) and return the first model. If no model is found, will return a "Void" model.

## Examples

Some examples for working with the Find Object:

```php
$orders = Order::
    ->where('name', 'John')
    ->whereRaw('Order.type = ? OR price < ?', ['big', 20.59]);

// SELECT Oroder.* FROM Order WHERE name = 'John' AND (Order.type = "big" OR price < 20.59)
foreach ($orders->load() as $order) {
    var_dump($order);
}
```

Joining tables with an alias

```php
$orders = Order::findAll()
    ->joinAliased('Customer', 'customers', ['Order.id' => 'customers.orderId', 'type' => '"big"'], 'LEFT')
    ->order('createdAt', 'id')
    ->limit(10)
    ->group('id');

// SELECT Oroder.* FROM Order
// LEFT JOIN Customer AS customers ON Order.id = customers.orderId AND type = "big"
// GROUP BY id
// LIMIT 10
foreach ($orders->load() as $order) {
    var_dump($order);
}
```

Counting items

```php
// SELECT COUNT(Order.id) AS countAll FROM Order WHERE name LIKE '%test'
$count = Order::findAll()
    ->whereLike('name', '%test')
    ->loadCount();
```

Eager Loading

```php
// SELECT Order.* FROM Order WHERE number = '3231'
// SELECT Customer.* FROM Customer WHERE customerId IN (3, 5, 4 ...)
// SELECT Address.* FROM Address WHERE customerId IN (3, 5, 4 ...)
// SELECT Country.* FROM Address WHERE addressId IN (2, 3, 9 ...)
$orders = Order::findAll()
    ->where('number' => '3231')
    ->loadWith(['customer', 'address' => 'country']);

foreach ($orders as $order) {
    var_dump($order);
    var_dump($order->getCustomer());
    var_dump($order->getAddress());
    var_dump($order->getAddress()->getCountry());
}
```

Shortcut Methods
----------------

There are some shortcut methods to make it quicker to find models.

```php
$orders = Order::where('name', 'test')->load();
$orders = Order::whereIn('name', ['test', 'test2'])->load();
$orders = Order::whereNot('name', 'test')->load();
$orders = Order::whereLike('name', 'test')->load();
$orders = Order::whereRaw('id < ?', [2])->load();
```
