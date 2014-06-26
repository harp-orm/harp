# Inherited

Sometimes you need several models to share the same database table - e.g. if there is just a slight variation of the same functionality. This is called Single Table Inheritence.

To use this you need to have a "root" model and all the other models will extend it. Also you need to have a column in the table that states exactly which model class it is.

Here's how you do this:

__Database Table:__

```
┌─────────────────────────┐
│ Table: Order            │
├─────────────┬───────────┤
│ id          │ ingeter   │
│ class       │ string    │
│ orderKey    │ string    │
└─────────────┴───────────┘
```

```php
// Model File
use Harp\Harp\AbstractModel;
use Harp\Core\Model\InheritedTrait;

class Order extends AbstractModel
{
    const REPO = 'OrderRepo';

    use InheritedTrait;

    public $id;
    public $orderKey;
}

// Root Repo File
use Harp\Harp\AbstractRepo;

class OrderRepo extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Order')
            ->setInherited(true);
    }
}

// Child Model File
class BankOrder extends Order
{
    const REPO = 'BankOrderRepo';
}

// Child Repo File
class BankOrderRepo extends BankOrder {

    public function initialize()
    {
        parent::initialize();

        $this
            ->setModelClass('BankOrder')
            ->setRootRepo(OrderRepo::get());
    }
}

// If this is a BankOrder it will return an actual BankOrder model
// Otherwise it will be just Order model
$order = Order::find(5);
```


