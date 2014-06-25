![](https://avatars3.githubusercontent.com/u/7734316?s=140 "Harp ORM Logo")

# Harp ORM

[![Build Status](https://travis-ci.org/harp-orm/harp.svg?branch=master)](https://travis-ci.org/harp-orm/harp)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/harp-orm/harp/badges/quality-score.png?s=57a2e2c70f39e76cd55d7d6d7938a56404deb468)](https://scrutinizer-ci.com/g/harp-orm/harp/)
[![Code Coverage](https://scrutinizer-ci.com/g/harp-orm/harp/badges/coverage.png?s=ffc98c29ef43ccddf14b2df890f230a9c1d99e18)](https://scrutinizer-ci.com/g/harp-orm/harp/)
[![Latest Stable Version](https://poser.pugx.org/openbuildings/harp/v/stable.svg)](https://packagist.org/packages/harp-orm/harp)

Harp ORM is a light DataMapper persistence layer for php objects.

## Quick example

```php
// Model Class
use Harp\Harp\AbstractModel;

class UserModel extends AbstractModel
{
    const REPO = 'UserRepo';

    public $id;
    public $name;
    public $email;
    public $addressId;

    public function getAddress()
    {
        return $this->getLink('address')->get();
    }
}

// Repo Class
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel;

class UserRepo extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelClass('UserModel')
            ->addRel(new Rel\BelongsTo('address', $this, AddressRepo::get()));
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

## Defining Models

Each model has at least 2 classes associated with it. The model itself, and a "repo" class. The repo class holds all the static information about the models, its relation graph, validation rules, how it gets persisted in the database etc. Its a singleton class, so all models for one table belong to one repo object.

Since Harp ORM does not enforce a folder structure, each model must have a link to its repo and vice versa.

You will need to add a const "REPO" in your model, and call setModelClass in the repo, so that they can know about each other.

```php
// in the Model ...
const REPO = 'UserRepo';

// in the Repo
->setModelClass('UserModel');
```

### The model class

Here's an example model class.

```php
use Harp\Harp\AbstractModel;

class User extends AbstractModel
{
    // Repo class
    const REPO = 'UserRepo';

    // Public properties persisted as columns in the table
    public $id;
    public $name;
    public $email;
    public $addressId;
}
```

All the public properties get persisted in the database, using the native types if available.

> __Tip__ Once related objects have been loaded, they will be cached and returned on further requests, however the data is not kept in the model itself, thus if you do a ``var_dump`` on an model it will return only data of the model itself and will keep your stack traces readable.

### The repo class

The repo class holds all the configuration of the corresponding model - table name, associations, validation, serialization etc. Here is a repo class for the model class above:

```php

use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

class UserRepo extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->setModelClass('User')
            ->addRel(new Rel\BelongsTo('address', $this, AddressRepo::get()));
            ->addAssert(new Assert\Present('name'))
            ->addAssert(new Assert\Email('name'));
    }
}
```

Configuration options:

- __setModelClass__($class) Set the corresponding model class, required
- __setTable__($table) Set the name of the database table, defaults to the short class name of the repo
- __setDb__($dbName) Set alternative database connection
- __setSoftDelete__($isSoftDelete) Set to true if you want this model to be soft deleted. More on [soft delete later](#soft-delete)
- __setInherited__($isInherited) Set to true if this repo will be inherited by other repo using [Single table inheritance](#single-table-inheritance.
- __setRootRepo__(AbstractRepo $repo) Used for children in single table inheritence
- __setPrimaryKey__($primaryKey) Sets the property/column to be used for primary key, "id" by default
- __setNameKey__($nameKey) Sets the property/column to be used for name key - will be used for findByName method on the repo. Defaults to "name"
- __addRel__(AbstractRel $rel) Add a link to a related model. Read about [relations](#relations)
- __addRels__(array $rels) Add multiple rels .
- __addAssert__(AbstractAssert $assert) Add an assertion for this model. Read about [assertions](#assertions)
- __addAsserts__(array $asserts) Add multiple asserts
- __addSerializer__(AbstractSerializer) Add a property serializer. Read about [serializers](#serializers)
- __addSerializers__(array $serializers) Add multiple serializers
- __addEventBefore__($event, $callback) Add event listener, to be triggered before a specific event
- __addEventAfter__($event, $callback) Add event listener to be triggered after a specific event

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

Here are the methods for building the query:

- __column__($column, $alias = null) - add a custom column to the list, if none is set, will use "table name".*
- __prependColumn__($column, $alias = null) - prepend a custom column to the list
- __where__($column, $value) - add a condition (=)
- __whereNot__($column, $value) - add a negative condition (!=)
- __whereIn__($column, array $values) - add "in array" condition (IN)
- __whereLike__($column, $value) - add a like condition (LIKE)
- __whereKey__($key) - add a condition for a primary key (id = value)
- __having__($column, $value) - add a condition (=) to HAVING
- __havingNot__($column, $value) - add a negative (!=) to HAVING
- __havingIn__($column, array $values) - add an "in array" condition (IN) to HAVING
- __havingLike__($column, $value) - add a like condition (LIKE) to HAVING
- __group__($column, $direction = null) - add a GROUP BY
- __order__($column, $direction = null) - add an ORDER BY
- __join__($table, $conditions, $type = null) - add a JOIN to another table
- __joinAliased__($table, $alias, $conditions, $type = null) - the same as __join__, but the table will have an alias
- __joinRels__(array $rels) join related models if they have a proper configuration. Argument can be a nested array.
- __limit__($limit)
- __offset__($offset)

## Persisting Models

When models have been created, modified or deleted they usually need to be persisted again. Since loading was done using static methods, saving must be done with static methods as well.

```php
$user = User::find(10);
$user->name = 'new name';

$address = new Model\Address(['location' => 'home']);
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

## License

Copyright (c) 2014, Clippings Ltd. Developed by Ivan Kerin as part of [clippings.com](http://clippings.com)

Under BSD-3-Clause license, read LICENSE file.

Icon made by [Freepik](http://www.freepik.com) from [www.flaticon.com](http://www.flaticon.com/free-icon/harp-outline_28540 "Flaticon")
