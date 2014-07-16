# Relations

When you configure your model repo objects you need to define the relationships with other models. This is done with the "Rel" objects which you add to the configuration.

These objects can are:

- [BelongsTo](#belongs-to)
- [HasOne](#has-one)
- [HasMany](#has-many)
- [HasManyThrough](#has-many-through)
- [BelongsToPolymorphic](#belongs-to-polymorphic)
- [HasManyExclusive](#has-many-exclusive)

## Belongs To

A BelongsTo relation sets up a one-to-one connection with another model, such that each instance of the declaring model "belongs to" one instance of the other model. For example, if your application includes customers and orders, and each order can be assigned to exactly one customer, you’d declare the order model this way:

__Database Tables:__
```
┌───────────────────────┐
│ Table: Order          │
│ BelongsTo: Customer   │    ┌───────────────────────┐
├─────────────┬─────────┤    │ Table: Customer       │
│ id          │ ingeter │    ├───────────────────────┤
│ customerId  │ ingeter │───>│ id          | ingeter │
│ orderKey    │ string  │    │ name        | string  │
└─────────────┴─────────┘    └───────────────────────┘
```

```php
// Model File
use Harp\Harp\AbstractModel;
use Harp\Harp\Rel\BelongsTo;

class Order extends AbstractModel
{
    public static function initialize($config)
    {
        $config
            ->addRel(new BelongsTo('customer', $config, Customer::getRepo()));
    }

    public $id;
    public $orderKey;
    public $customerId;

    public function getCustomer()
    {
        return $this->get('customer');
    }

    public function setCustomer(Customer $customer)
    {
        return $this->set('customer', $customer);
    }
}

// Working with BelongsTo relation
$customer = $order->getCustomer();
$order->setCustomer($customer2);
```

> __Tip__ Though you could use the ``get`` and ``set`` methods directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

By default the name of the column use for the foreign key is defined as "foreign model" + "Id", but you can configure that with a "key" option e.g.

```php
new BelongsTo('customer', $repo, Customer::getRepo(), ['key' => 'otherId'])
```

## Has One

HasOne relation also sets up a one-to-one connection with another model, but with somewhat different semantics (and consequences). This relation indicates that each instance of a model possesses one instance of another model. For example, if each supplier in your application has only one account, you'd declare the supplier model like this:

__Database Tables:__
```
┌───────────────────────┐    ┌───────────────────────┐
│ Table: Supplier       │    │ Table: Account        │
│ HasOne: Account       │    ├─────────────┬─────────│
├─────────────┬─────────┤    │ id          │ ingeter │
│ id          │ ingeter │◄───│ supplierId  │ ingeter │
│ name        │ string  │    │ accountNum  │ string  │
└─────────────┴─────────┘    └─────────────┴─────────┘
```

```php
// Model File
use Harp\Harp\AbstractModel;
use Harp\Harp\Rel\HasOne;

class Supplier extends AbstractModel
{
    public static function initialize($config)
    {
        $config
            ->addRel(new HasOne('account', $config, Account::getRepo()));
    }

    public $id;
    public $name;

    public function getAccount()
    {
        return $this->get('account');
    }

    public function setAccount(Account $account)
    {
        return $this->set('account', $account);
    }
}

// Working with BelongsTo relation
$account = $supplier->getAccount();
$supplier->setAccount($account2);
```

> __Tip__ Though you could use the ``get`` and ``set`` methods directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

By default the name of the column use for the foreign key is defined as "foreign model" + "Id", but you can configure that with a "foreignKey" option e.g.

```php
$rel = new HasOne('account', $this, AccountRepo::get(), ['foreignKey' => 'otherId']);
```

## Has Many

A ``HasMany`` relation indicates a one-to-many connection with another model. You'll often find this association on the "other side" of a ``BelongsTo`` relation. This relation indicates that each instance of the model has zero or more instances of another model. For example, in an application containing customers and orders, the customer model could be declared like this:

__Database Tables:__
```
┌───────────────────────┐    ┌───────────────────────┐
│ Table: Customer       │    │ Table: Order          │
│ HasMany: Orders       │    ├─────────────┬─────────┤
├─────────────┬─────────┤    │ id          │ ingeter │
│ id          │ ingeter │◄───│ supplierId  │ ingeter │
│ name        │ string  │    │ accountNum  │ string  │
└─────────────┴─────────┘    └─────────────┴─────────┘
```

```php
// Model File
use Harp\Harp\AbstractModel;
use Harp\Harp\Rel\HasMany;

class Customer extends AbstractModel
{
    public static function initialize($config)
    {
        $config
            ->addRel(new HasMany('orders', $config, Order::getRepo()));
    }

    public $id;
    public $name;

    public function getOrders()
    {
        return $this->all('orders');
    }
}

$orders = $customer->getOrders();
foreach ($orders as $order) {
    var_dump($order);
}
$customer->getOrders()->add($order2);
```

> __Tip__ Though you could use the ``all`` method directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

By default the name of the column use for the foreign key is defined as "foreign model" + "Id", but you can configure that with a "foreignKey" option e.g.

```php
$rel = new HasMany('orders', $this, OrderRepo::get(), ['foreignKey' => 'otherId']);
```

## Has Many Through

A ``HasManyThrough`` relation creates a many-to-many connection with another model. For example, if your application includes assemblies and parts, with each assembly having many parts and each part appearing in many assemblies. This requires a "through" model which is related with a ``HasMany`` relation - in this case AssemblyPart model. You could declare it this way:

__Database Tables:__
```
┌────────────────────────────┐
│ Table: Assembly            │
│ HasManyThrough: parts      │
│ HasMany: assemblyParts     │
├─────────────┬──────────────┤      ┌────────────────────────┐
│ id          │ ingeter      │◄──┐  │ Table: AssemblyPart    │
│ name        │ string       │   │  ├─────────────┬──────────┤
└─────────────┴──────────────┘   │  │ id          │ integer  │
                                 └──│ assemblyId  │ ingeter  │
┌────────────────────────────┐   ┌──│ partId      │ string   │
│ Table: Parts               │   │  └─────────────┴──────────┘
│ HasManyThrough: assemblies │   │
│ HasMany: assemblyParts     │   │
├─────────────┬──────────────┤   │
│ id          │ ingeter      │◄──┘
│ name        │ string       │
└─────────────┴──────────────┘
```

```php
// Model File
use Harp\Harp\AbstractModel;
use Harp\Harp\Rel\HasManyThrough;
use Harp\Harp\Rel\HasMany;

class Assembly extends AbstractModel
{
    public static function initialize($config)
    {
        $config
            ->addRels([
                new HasManyThrough('parts', $config, Part::getRepo(), 'assemblyParts')),
                new HasMany('assemblyParts', $config, AssemblyPart::getRepo()))
            ]);
    }

    public $id;
    public $name;

    public function getParts()
    {
        return $this->all('parts');
    }
}

$parts = $customer->getParts();
foreach ($parts as $part) {
    var_dump($part);
}
$customer->getParts()->add($part2);
```

> __Tip__ Though you could use the ``all`` method directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

By default the name of the columns use for the foreign keys in the "through" model are "model" + "Id" and "foreign model"  + "Id", but you can configure that with a "key" and "foreignKey" options e.g.

```php
new HasManyThrough(
    'parts',
    $config,
    Part::getRepo(),
    'assemblyParts',
    [
        'key' => 'otherAssemblyId',
        'foreignKey' => 'otherPartId',
    ]
));
```

## Has Many Exclusive

The "HasManyExclusive" relation is exactly the same as HasMany, with one exception. When a model is removed from the relation, it is deleted.

```php
// Repo File
use Harp\Harp\AbstractModel;
use Harp\Harp\Rel\HasManyExclusive;

class Customer extends AbstractModel {

    public static function initialize($config)
    {
        $config
            ->addRel(new HasManyExclusive('orders', $config, Order::getRepo()));
    }
}
```

## Belongs To Polymorphic

A slightly more advanced twist on relations is the ``BlongsToPolymorphic`` relation. With polymorphic associations, a model can belong to more than one other model, on a single association. For example, you might have a picture model that belongs to either an employee model or a product model. Here's how this could be declared:

__Database Tables:__
```
┌───────────────────────────────────┐
│ Table: Employee                   │
│ HasManyAs: pictures, parent       │      ┌──────────────────────────────┐
├─────────────────────────┬─────────┤      │ Table: Pircture              │
│ id                      │ ingeter │◄──┐  │ BelongsToPolymorphic: parent │
│ name                    │ string  │   │  ├────────────────┬─────────────┤
└─────────────────────────┴─────────┘   │  │ id             │ ingeter     │
                                        │  │ name           │ string      │
┌───────────────────────────────────┐   ├──│ parentId       │ ingeter     │
│ Table: Product                    │   │  │ parentClass    │ string      │
│ HasManyAs: pictures, parent       │   │  └────────────────┴─────────────┘
├─────────────────────────┬─────────┤   │
│ id                      │ ingeter │◄──┘
│ name                    │ string  │
└─────────────────────────┴─────────┘
```
```php
class Picture extends AbstractModel
{
    public static function initialize($config)
    {
        $config
            ->addRel(new BelongsToPolymorphic('parent', $config, Product::getRepo());
    }

    public $id;
    public $name;
    public $parentId;
    public $parentClass;
}

class Employee extends AbstractModel
{
    public static function initialize($config)
    {
        $config
            ->addRel(new HasManyAs('pictures', $config, Picture::getRepo(), 'parent');
    }

    public $id;
    public $name;
}

class Product extends AbstractModel
{
    public function initialize($config)
    {
        $config
            ->addRel(new HasManyAs('pictures', $config, Picture::getRepo(), 'parent');
    }

    public $id;
    public $name;
}
```

You can think of a polymorphic belongsto declaration as setting up an interface that any other model can use. From an instance of the ``Employee`` model, you can retrieve a collection of pictures: ``$employee->getPictures()``.

Similarly, you can retrieve ``$product->getPictures()``.

If you have an instance of the ``Picture`` model, you can get to its parent via ``$picture->getParent()``.

## Inverse Relations

When you set up relations you might want to specify the "inverse" relation also, so it will be set properly when working with unsaved models. This works only on RelOne inverse relations.

Here's where this is useful:

```php
class User extends AbstractModel
{
    public static function initialize($config)
    {
        $config->addRel(new HasMany('posts', $config, Post::getRepo(), ['inverseOf' => 'user']);
    }
}

class Post extends AbstractModel
{
    public static function initialize($config)
    {
        $config->addRel(new HasMany('user', $config, User::getRepo());
    }
}

$user = new User();
$post = new Post();

$user->getPosts()->add($post);

// This will be true
$post->getUser() === $user;
```

## Working with relation collections

HasMany, HasManyThrough and other relations to multiple models will return a "LinkMany" object which is used to add, remove or otherwise manipulate the relation. The object is an iterator and implements countable as well, so you can call ``count($products)`` as well as put it directly in a ``foreach``.

```php
$products = $store->get('products');

$newProduct = new Product();

$products->add($newProduct);
```

Method                             | Description
-----------------------------------|------------------------------------------------
__has__(AbstractModel $model)      | Check if a model is in this collection of models
__getRel__()                       | Get the relation object
__get__()                          | Get the internal Models object, holding information of the current state of the relation
__getOriginal__()                  | Get the internal Models object, with the models that were loaded originally from the database
__isChanged__()                    | Check if any models have been added / removed from the relation
__getAdded__()                     | Get a Models object containing all the models added to this relation
__getRemoved__()                   | Get a Models object containing all the models removed from this relation
__getFirst__()                     | Get the first model in the collection of this relation. Will return a void model if the collection is empty
__getNext__()                      | Get the next model in the collection of this relation, after a call getFirst. Will return a void model when the end of the collection is reached
__addArray__(array $models)        | Add several models to the relation at once using an array
__addModels__(Models $models)      | Add several models to the relation at once using an Models object
__add__(AbstractModel $model)      | Add a model to the relation
__toArray__()                      | Get an array with all the models in the colleciton
__remove__(AbstractModel $model)   | Remove a model from the relation
__isEmpty__()                      | Return true if the relation is empty
__clear__()                        | Remove all the models from this relation.
__has__(AbstractModel $model)      | Return true if the model is in this relation.
__filter__(Closure $filter)        | Filter the models and return a new collection with the models, for which the filter closure has returned true
__invoke__($method)                | Call a method for each model and return an array of all the results
__map__(Closure $map)              | Call a closure for each item and return an array with the result.


