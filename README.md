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

- Uses harp-orm/query and PDO as much as possible greatly increasing performance. It has some very useful features that are not used by current ORMs
- Full polymorphism support - both "belongs to polymorphic" and "single table inheritance"
- Proper soft deletes, which every part of the code is aware of
- Lazy loading and eager loading, which works for polymorphic relations
- Save multiple models with grouped queries for increased performance
- No enforcement of folder structure, place your classes wherever you want
- Uses PSR coding style and symfony naming conventions for more clean and readable codebase
- Save all associated models with a single command, with query grouping under the hood.
- Fully extensible interface. Uses native PHP5 constructs to allow extending with traits and interfaces
- All methods have proper docblocks so that static code analyses of code built on top of this is more accurate.

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
            ->addRel(new Rel\BelongsTo('address', $config, AddressRepo::get()));
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
__setDb__($dbName)                     | Set alternative database connection, this will tap into alternative database configurations you've setup
__setSoftDelete__($isSoftDelete)       | Set to true if you want this model to be soft deleted. More on [soft delete later](/docs/SoftDelete.md)
__setInherited__($isInherited)         | Set to true if this repo will be inherited by other repo using [Single table inheritance](/docs/Inherited.md)
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

Detailed [docs for findAll](/docs/Find.md)

## Persisting Models

When models have been created, modified or deleted they usually need to be persisted again. Since loading was done using static methods, saving must be done with static methods as well.

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

        // ...
    }

    // ...
}
```

This adds a some of methods to your model. Read about [soft deletion in detail here](/docs/SoftDelete.md)

## Inherited

Sometimes you need several models to share the same database table - e.g. if there is just a slight variation of the same functionality. This is called Single Table Inheritance.

Harp ORM supports inheriting models (and repos) out of the box. Read about [inexperience in detail here](/docs/Inherited.md)

## Extending

When you want to write packages that extend functionality of Harp ORM, or simple share code between your models, you can use [PHP's native Traits](http://www.php.net/manual/en/language.oop5.traits.php). They allow you to statically extends classes. All of the internals of Harp ORM are built around allowing you to accomplish this easily as this is the preferred way of writing "behaviours/templates".

Apart from that you will be able to add event listeners for various events in the life-cycle of models.

Read about [extending in detail here](/docs/Extending.md)

## License

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.

Icon made by [Freepik](http://www.freepik.com) from [www.flaticon.com](http://www.flaticon.com/free-icon/harp-outline_28540 "Flaticon")
