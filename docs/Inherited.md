# Inherited

Sometimes you need several models to share the same database table - e.g. if there is just a slight variation of the same functionality. This is called Single Table Inheritance.

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
use Harp\Harp\Model\InheritedTrait;
use Harp\Harp\Repo;

class Order extends AbstractModel
{
    use InheritedTrait;

    public static function initialize(Repo $repo)
    {
        InheritedTrait::initialize($repo);
    }

    public $id;
    public $orderKey;
}

// Child Model File
class BankOrder extends Order
{
    public static function initialize(Repo $repo)
    {
        parent::initialize($repo);
    }
}

// If this is a BankOrder it will return an actual BankOrder model
// Otherwise it will be just Order model
$order = Order::find(5);
```


