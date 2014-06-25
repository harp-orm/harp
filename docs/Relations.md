
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
        return $this->getLink('customer')->get();
    }

    public function setCustomer(Customer $customer)
    {
        return $this->getLink('customer')->set($customer);
    }
}

// Repo File
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel\BelongsTo;

class OrderRepo extends AbstractRepo {

    public function initialize()
    {
        $this
            ->addRel(new BelongsTo('customer', $this, CustomerRepo::get()));
    }
}

// Working with BelongsTo relation
$customer = $order->getCustomer();
$order->setCustomer($customer2);
```

> __Tip__ Though you could use the ``getLink`` method directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

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
        return $this->getLink('account')->get();
    }

    public function setAccount(Account $account)
    {
        return $this->getLink('account')->set($account);
    }
}

// Repo File
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel\HasOne;

class SupplierRepo extends AbstractRepo {

    public function initialize()
    {
        $this
            ->addRel(new HasOne('account', $this, AccountRepo::get()));
    }
}

// Working with BelongsTo relation
$account = $supplier->getAccount();
$supplier->setAccount($account2);
```

> __Tip__ Though you could use the ``getLink`` method directly to retrieve / change data, it is better to define specific methods for each relation, as is the case in the example above.

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
        return $this->getLink('orders');
    }
}

// Repo File
use Harp\Harp\AbstractRepo;
use Harp\Harp\Rel\HasMany;

class CustomerRepo extends AbstractRepo {

    public function initialize()
    {
        $this
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

## Has Many Through

A ``HasManyThrough`` relation creates a many-to-many connection with another model. For example, if your application includes assemblies and parts, with each assembly having many parts and each part appearing in many assemblies, you could declare the models this way:

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
        return $this->getLink('parts');
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
