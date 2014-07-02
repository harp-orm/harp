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

class Order extends AbstractModel
{
    const REPO = 'OrderRepo';

    public $id;
    public $orderKey;
    public $customerId;

    public function getCustomer()
    {
        return $this->getLinkedModel('customer');
    }

    public function setCustomer(Customer $customer)
    {
        return $this->setLinkedModel('customer', $customer);
    }
}

// Repo File
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel\BelongsTo;

class OrderRepo extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Order')
            ->addRel(new BelongsTo('customer', $this, CustomerRepo::get()));
    }
}

// Working with BelongsTo relation
$customer = $order->getCustomer();
$order->setCustomer($customer2);
```

> __Tip__ Though you could use the ``getLink`` method directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

By default the name of the column use for the foreign key is defined as "foreign model" + "Id", but you can configure that with a "key" option e.g.

```php
$rel = new BelongsTo('customer', $this, CustomerRepo::get(), ['key' => 'otherId']);
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

class Supplier extends AbstractModel
{
    const REPO = 'SupplierRepo';

    public $id;
    public $name;

    public function getAccount()
    {
        return $this->getLinkedModel('account');
    }

    public function setAccount(Account $account)
    {
        return $this->setLinkedModel('account', $account);
    }
}

// Repo File
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel\HasOne;

class SupplierRepo extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Supplier')
            ->addRel(new HasOne('account', $this, AccountRepo::get()));
    }
}

// Working with BelongsTo relation
$account = $supplier->getAccount();
$supplier->setAccount($account2);
```

> __Tip__ Though you could use the ``getLink`` method directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

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

class Customer extends AbstractModel
{
    const REPO = 'CustomerRepo';

    public $id;
    public $name;

    public function getOrders()
    {
        return $this->getLinkMany('orders');
    }
}

// Repo File
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel\HasMany;

class CustomerRepo extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Customer')
            ->addRel(new HasMany('orders', $this, OrderRepo::get()));
    }
}

$orders = $customer->getOrders();
foreach ($orders as $order) {
    var_dump($order);
}
$customer->getOrders()->add($order2);
```

> __Tip__ Though you could use the ``getLink`` method directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

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

class Assembly extends AbstractModel
{
    const REPO = 'AssemblyRepo';

    public $id;
    public $name;

    public function getParts()
    {
        return $this->getLinkMany('parts');
    }
}

// Repo File
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel\HasManyThrough;
use Harp\Harp\Rel\HasMany;

class AssemblyRepo extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Assembly')
            ->addRels([
                new HasManyThrough('parts', $this, PartRepo::get(), 'assemblyParts')),
                new HasMany('assemblyParts', $this, AssemblyPartRepo::get()))
            ]);
    }
}

$parts = $customer->getParts();
foreach ($parts as $part) {
    var_dump($part);
}
$customer->getParts()->add($part2);
```

> __Tip__ Though you could use the ``getLink`` method directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

By default the name of the columns use for the foreign keys in the "through" model are "model" + "Id" and "foreign model"  + "Id", but you can configure that with a "key" and "foreignKey" options e.g.

```php
$rel = new HasManyThrough(
    'parts',
    $this,
    PartRepo::get(),
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
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel\HasMany;

class CustomerRepo extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Customer')
            ->addRel(new HasManyExclusive('orders', $this, OrderRepo::get()));
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
class PictureRepo extends AbstractRepo
{
    public function initialize()
    {
        // ...
        $this
            ->addRel(new BelongsToPolymorphic('parent', $this, ProductRepo::get());
    }
}

class EmployeeRepo extends AbstractRepo
{
    public function initialize()
    {
        // ...
        $this
            ->addRel(new HasManyAs('pictures', $this, PictureRepo::get(), 'parent');
    }
}

class ProductRepo extends AbstractRepo
{
    public function initialize()
    {
        // ...
        $this
            ->addRel(new HasManyAs('pictures', $this, PictureRepo::get(), 'parent');
    }
}
```

You can think of a polymorphic belongsto declaration as setting up an interface that any other model can use. From an instance of the ``Employee`` model, you can retrieve a collection of pictures: ``$employee->getPictures()``.

Similarly, you can retrieve ``$product->getPictures()``.

If you have an instance of the ``Picture`` model, you can get to its parent via ``$picture->getParent()``.

