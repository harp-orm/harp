## Query

Internal Query objects extend corresponding Query objects from [harp-orm/query](https://github.com/harp-orm/query). You can use them directly by calling the methods.

Method        | Description
--------------|------------------------------------------------
__updateAll__ | Return an Update Query object for the given model
__selectAll__ | Return an Select Query object for the given model
__insertAll__ | Return an Insert Query object for the given model
__deleteAll__ | Return an Delete Query object for the given model

## Select

To create a Select object for a specific Model, you can use the ``selectAll`` static method. This will pre-populate the "from" and "columns" parts of the query.

Executing the object will return a [PDOStatement](http://php.net/manual/en/class.pdostatement.php) object which you can foreach, or call fetchAll.

```php
$select = User::selectAll();

// SELECT User.* FROM User JOIN Profile ON Profile.id = profileId WHERE id IN (1, 2, 3) AND Profile.name LIKE '%test'
$result = $select
    ->join('Profile', ['Profile.id' => 'profileId'])
    ->whereIn('id', [1, 2, 3])
    ->whereLike('Profile.name', '%test')
    ->execute();

foreach ($result as $row)
{
    echo $row['name'];
}
```

You can read the [docs for Select Query here](harp-orm/query/master/docs/Select.md)

In addition to that there are several more methods available:

Method                           | Description
---------------------------------|------------------------------------------------
__getRepo__()                    | Get the repo for the corresponding model
__joinRels__(array|string $rels) | Perform join on relations, configured in the repo.


## Update

To create an Update object for a specific Model, you can use the ``updateAll`` static method. This will pre-populate the "table" part of the query.

```php
$update = User::updateAll();

// UPDATE TABLE User SET name = 'new name' WHERE id IN (1, 2, 3)
$update
    ->set(['name' => 'new name'])
    ->whereIn('id', [1, 2, 3])
    ->execute();
```

You can read the [docs for Update Query here](harp-orm/query/master/docs/Update.md)

In addition to that there are several more methods available:

Method                           | Description
---------------------------------|------------------------------------------------
__getRepo__()                    | Get the repo for the corresponding model
__joinRels__(array|string $rels) | Perform join on relations, configured in the repo.
__model__(AbstractModel $model)  | Set the "SET" part of the query with the changes of the model, and the WHERE part of the query with the id of the model
__models__(array $models)        | Set the "SET" and "WHERE" parts of the query in order to update all the rows associated with the models.

## Delete

To create a Delete object for a specific Model, you can use the ``deleteAll`` static method. This will pre-populate the "table" part of the query.

```php
$delete = User::deleteAll();

// UPDATE TABLE User SET name = 'new name' WHERE id IN (1, 2, 3)
$delete
    ->whereIn('id', [1, 2, 3])
    ->execute();
```

You can read the [docs for Delete Query here](harp-orm/query/master/docs/Delete.md)

In addition to that there are several more methods available:

Method                           | Description
---------------------------------|------------------------------------------------
__getRepo__()                    | Get the repo for the corresponding model
__joinRels__(array|string $rels) | Perform join on relations, configured in the repo.
WHERE part of the query with the id of the model
__models__(array $models)        | Set the  "WHERE" part of the query in order to delete all the rows associated with the models.


## Insert

To create a Insert object for a specific Model, you can use the ``insertAll`` static method. This will pre-populate the "table" part of the query.

```php
$insert = User::insertAll();

// INSERT INTO User (id, 'name', 'email')
// VALUES
// (1, 'John', 'john@example.com'),
// (2, 'Julie', 'julie@example.com'),
// (3, 'Tom', 'tom@example.com');
$insert
    ->columns(['id', 'name', 'email'])
    ->values([1, 'John', 'john@example.com'])
    ->values([2, 'Julie', 'julie@example.com'])
    ->values([3, 'Tom', 'tom@example.com'])
    ->execute();
```

You can read the [docs for Insert Query here](harp-orm/query/master/docs/Insert.md)

In addition to that there are several more methods available:

Method                           | Description
---------------------------------|------------------------------------------------
__getRepo__()                    | Get the repo for the corresponding model
__models__(array $models)        | Set the  "COLUMNS" and "VALUES" parts of the query in order to insert all the rows associated with the models.


