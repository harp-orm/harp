![](https://avatars3.githubusercontent.com/u/7734316?s=140 "Harp ORM Logo")

# Harp ORM

[![Build Status](https://travis-ci.org/harp-orm/harp.svg?branch=master)](https://travis-ci.org/harp-orm/harp)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/harp-orm/harp/badges/quality-score.png)](https://scrutinizer-ci.com/g/harp-orm/harp/)
[![Code Coverage](https://scrutinizer-ci.com/g/harp-orm/harp/badges/coverage.png)](https://scrutinizer-ci.com/g/harp-orm/harp/)
[![Latest Stable Version](https://poser.pugx.org/harp-orm/harp/v/stable.svg)](https://packagist.org/packages/harp-orm/harp)

Harp ORM is a light DataMapper persistence layer for php objects.

## Quick example

```php
// Model Class
use Harp\Harp\AbstractModel;

class UserModel extends AbstractModel
{
    public static function initialize($config)
    {
        $config
            ->addRel(new Rel\BelongsTo('address', $config, Address::getRepo()));
    }

    public $id;
    public $name;
    public $email;
    public $addressId;

    public function getAddress()
    {
        return $this->get('address');
    }

    public function setAddress(Address $address)
    {
        return $this->set('address', $address);
    }
}

// Saving new model
$user = new UserModel(['name' => 'my name']);
UserRepo::save($user);

// Loading model
$loadedUser = UserRepo::find(1);
var_dump($loadedUser);

// Loading related model
$address = $loadedUser->getAddress();
var_dump($loadedUser->getAddress());
```

## Why?

Why another ORM? At present there are no ORMs that use the latest PHP features. Recent advancements in the php language itself (e.g. traits), and external static analysis tools allow for writing applications that can be easily verified to be correct, however currently available ORMs don't use them which makes static code analysis not very useful. This package aims to fix this. Here's the elevator pitch:

- Uses harp-orm/query and __PDO__ as much as possible greatly increasing performance. It has some very useful features that are not used by current ORMs
- Full __polymorphism__ support - both "belongs to polymorphic" and "single table inheritance"
- Proper __soft delete__, which every part of the code is aware of
- __Lazy loading__ and __eager loading__, which works for polymorphic relations
- __Save multiple models__ with __grouped queries__ for increased performance
- No enforcement of folder structure, __place your classes wherever you want__
- Uses __PSR coding style__ and __symfony naming conventions__ for more clean and readable codebase
- Save all associated models with a single command, with __query grouping__ under the hood.
- Fully extensible interface. Uses native PHP5 constructs to allow __extending with traits and interfaces__
- All methods have __proper docblocks__ so that static code analyses of code built on top of this is more accurate.

## Instalation

Harp uses composer so intalling it is as easy as:

```bash
composer require harp-orm/harp:~0.3.0
```

It uses [harp-orm/query](http://github.com/harp-orm/query) for connecting to the database, so you'll also need to configure the connection:

```php
use Harp\Query\DB;

DB::setConfig([
    'dsn' => 'mysql:dbname=harp-orm/harp;host=127.0.0.1',
    'username' => 'root',
    'password' => 'root',
]);
```

## Subsections

- [Find](/docs/Find.md)
- [Inherited](/docs/Inherited.md)
- [Relations](/docs/Relations.md)
- [Repo](/docs/Repo.md)
- [SoftDelete](/docs/SoftDelete.md)
- [Extending](/docs/Extending.md)

## Defining Models

Here's an example model class.

```php
use Harp\Harp\AbstractModel;

class User extends AbstractModel
{
    // Configure the "Repo" object for this model class
    // This holds all the database-specific configs,
    // as well as relations with other models
    public static function initialize($config)
    {
        $config

            // Configure relations
            ->addRel(new Rel\BelongsTo('address', $config, AddressRepo::get()));

            // Configure validations
            ->addAssert(new Assert\Present('name'))
            ->addAssert(new Assert\Email('name'));
    }

    // Public properties persisted as columns in the table
    public $id;
    public $name;
    public $email;
    public $addressId;
}
```

All the public properties get persisted in the database, using the native types if available.

> __Tip__ Once related objects have been loaded, they will be cached and returned on further requests, however the data is not kept in the model itself, thus if you do a ``var_dump`` on an model it will return only data of the model itself and will keep your stack traces readable.

Detailed list of all the configuration methods:

Configuration Method                   | Description
---------------------------------------|-------------
__setTable__($table)                   | Set the name of the database table, defaults to the short class name of the model
__setDb__($dbName)                     | Set alternative database connection, this will use alternative database configurations you've setup
__setSoftDelete__($isSoftDelete)       | Set to true if you want this model to be soft deleted. This is configured automatically by the SoftDeleteTrait. More on [soft delete later](/docs/SoftDelete.md)
__setInherited__($isInherited)         | Set to true if this repo will be inherited by other repo using [Single table inheritance](/docs/Inherited.md). This is configured automatically by the InheritedTrait.
__setPrimaryKey__($primaryKey)         | Sets the property/column to be used for primary key, "id" by default
__setNameKey__($nameKey)               | Sets the property/column to be used for name key - will be used for findByName method on the repo. Defaults to "name"
__addRel__(AbstractRel $rel)           | Add a link to a related model. Read about [Relations](/docs/Relations.md)
__addRels__(array $rels)               | Add multiple rel objects.
__addAssert__(AbstractAssert $assert)  | Add an assertion for this model. Read about [Assertions](/docs/Assertions.md)
__addAsserts__(array $asserts)         | Add multiple asserts
__addSerializer__(AbstractSerializer)  | Add a property serializer. Read about [Serializers](/docs/Serializers.md)
__addSerializers__(array $serializers) | Add multiple serializer objects
__addEventBefore__($event, $callback)  | Add event listener, to be triggered before a specific event
__addEventAfter__($event, $callback)   | Add event listener to be triggered after a specific event

## Retrieving from the database

Retrieving models from the database (as well as saving but on that later) are handled with static methods on the model class. To find models by their primary key use the ``find`` method.

```php
$user1 = User::find(8);
$user2 = User::find(23);
```

If the model has a "name" property (or a nameKey configured) you can use ``findByName`` method.

```php
$user1 = User::findByName('Tom');
$user2 = User::findByName('John');
```

For more complex retrieving you can use the ``findAll`` method, which returns a 'Find' object. It has a rich interface of methods for constructing an sql query:

```php
$select = User::findAll();
$select
    ->where('name', 'John')
    ->whereIn('type', [1, 4])
    ->joinRels(['address' => 'city'])
    ->limit(10);

$users = $select->load();

foreach ($users as $user) {
    var_dump($user);
}
```

All the models retrieved from the database are stored in an "identity map". So that if at a later time, the same model is loaded again. It will return the same php object, associated with the db row.

```php
$user1 = User::find(10);
$user2 = User::find(10);

// Will return true
echo $user1 === $user2;
```

Detailed [docs for findAll](/docs/Find.md)

## Persisting Models

When models have been created, modified or deleted they usually need to be persisted again. This is done with the "save" method on the model.

```php
$user = User::find(10);
$user->name = 'new name';

$address = new Address(['location' => 'home']);
$user->setAddress($address);

// This will save the user, the address and the link between them.
User::save($user);

$user2 = User::find(20);
$user2->delete();

// This will remove the deleted user from the database.
User::save($user2);
```

When you add / remove or otherwise modify related models they will be saved alongside your main model.

### Preserving multiple models

You can presever multiple models of the same repo at once (with query grouping) using the ``saveArray`` method. Just pass an array of models.

```php
$save = User::saveArray([$user1, $user2, $user3]);
```

## Soft deletion

If you need to keep the models in the database even after they are deleted - e.g. logical deltion, you can use the ``SoftDeleteTrait``

```php
// Model File
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\SoftDeleteTrait;

class Order extends AbstractModel
{
    use SoftDeleteTrait;

    public static function initialize($config)
    {
        // The trait has its own initialize method that you'll have to call
        SoftDeleteTrait::initialize($config);
    }
}

$order = Order::find(2);

$order->delete();

// This will issue an UPDATE instaead of a DELETE, marking this row as "deleted".
Order::save($order);

$order = Order::find(2);

// This will return true
echo $order->isVoid();
```

This adds a some of methods to your model. Read about [soft deletion in detail here](/docs/SoftDelete.md)

## Inherited

Sometimes you need several models to share the same database table - e.g. if there is just a slight variation of the same functionality. This is called Single Table Inheritance.

Harp ORM supports inheriting models out of the box. Read about [inexperience in detail here](/docs/Inherited.md)

## Model states

Throughout their lives Models have different states (Harp\Harp\Model\State). They can be:

State             | Description
------------------|--------------------
State::PENDING    | Still not persisted in the database. This is the default state of models. Will be inserted in the database when persisted
State::DELETED    | Marked for deletion. When persisted will execute a DELETE query
State::SAVED      | Retrieved from the database. When is changed
State::VOID       | This represents a "non-existing" model, e.g. when you try to retrieve a model that does not exists

There are several methods for working with model states:

Method                | Description
----------------------|-------------
__setState__($state)  | Set the state on the model
__getState__()        | Retrieve the current state
__isSaved__()         | Return true if state is State::SAVED
__isPending__()       | Return true if state is State::PENDING
__isDeleted__()       | Return true if state is State::DELETED
__isVoid__()          | Return true if state is State::VOID

## Dirty Tracking

Models track all the changes to their public properties to minimise updates to the database. You can use that functionality yourself by calling these methods:

Method                               | Description
-------------------------------------|------------------
__getOriginals__()                   | Get an array with all the original values of the properties
__getOriginal__($name)               | Get a specific original value, returns null if value does not exist
__getChange__($name)                 | Returns an array with [original, changed], or null if there is no change
__getChanges__()                     | Return an array with [name => new value] for all the changes
__hasChange__($name)                 | Return true if the property has been changed
__isEmptyChanges__()                 | Return true if there are __no__ changes
__isChanged__()                      | Return true if there are __any__ changes
__resetOriginals__()                 | Set the current property values as the "original". This is called when a model has been saved
__getProperties__()                  | Get an array with the current values of all the public properties
__setProperties__(array $properties) | Set public properties with a [property name => value] array

Example:

```php
$user = User::find(10);

// returns false
echo $user->isChanged();

$user->name = 'new test';

// returns true
$user->isChanged();

// returns true
$user->hasChange('name');

// returns ['name' => 'new test']
$user->getChanges();

// returns original name
$user->getOriginal('name');

$user->resetOriginal();

// returns 'new test'
$user->getOriginal('name');
```

## Extending

When you want to write packages that extend functionality of Harp ORM, or simply share code between your models, you can use [PHP's native Traits](http://www.php.net/manual/en/language.oop5.traits.php). They allow you to statically extends classes. All of the internals of Harp ORM are built around allowing you to accomplish this easily as this is the preferred way of writing "behaviours" or "templates".

Apart from that you will be able to add event listeners for various events in the life-cycle of models.

Read about [extending in detail here](/docs/Extending.md)

## Direct database access.

There are times when you'll need to get to the bare metal and write custom sqls. To help you do that you can use the internal Query classes directly.

```php
$update = User::insertAll()
    ->columns(['name', 'id'])
    ->select(
        Profile::selectAll()
            ->clearColumns()
            ->column('name')
            ->column('id')
            ->where('name', 'LIKE', '%test')
    );

// INSERT INTO User (name, id) SELECT name, id FROM Profile WHERE name LIKE '%test'
$update->execute();
```

More details about custom queries you can read the [Query section](/docs/Query.md)

## License

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.

Icon made by [Freepik](http://www.freepik.com) from [www.flaticon.com](http://www.flaticon.com/free-icon/harp-outline_28540 "Flaticon")
