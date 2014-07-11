# Soft Delete

Sometimes you need to "delete" models without actually deleting them from the database. This is called soft or logical delete. It involves setting a "deletedAt" column with the date when the deletion takes place. After that all the selects and joins will ignore this row as if it did not exist. You can then restore the row at later date, if it is needed.

In order to implement "soft delete" you need to add the ``SoftDeleteTrait`` to the model, and call ``setSoftDelete(true)`` in the repo:

__Database Table:__

```
┌─────────────────────────┐
│ Table: Order            │
├─────────────┬───────────┤
│ id          │ ingeter   │
│ deletedAt   │ timestamp │
│ orderKey    │ string    │
└─────────────┴───────────┘
```

```php
// Model File
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\SoftDeleteTrait;

class Order extends AbstractModel
{
    use SoftDeleteTrait;

    public static function initialize($repo)
    {
        SoftDeleteTrait::initialize($repo);
    }

    public $id;
    public $orderKey;
}
```

The ``SoftDeleteTrait`` overrides the normal delete method, and gives you some other methods too.

```
$order = Order::find(2);
$order->delete();

// This will call UPDATE Order SET deletedAt = '{current time}' WHERE id = 2
Order::save($order);

// After that the model is considered "deleted"
$order = Order::find(2);
echo $order->isVoid(); // Will return true

// You can force finding of deleted models
use Harp\Harp\Model\State;
$order = Order::find(2, State::DELETED);
$order = Order::find(2, State::DELETED | State::SAVED);

// This can also be accomplished like this:
$order = Order::findAll()->onlyDeleted()->whereKey(2)->loadFirst();
$order = Order::findAll()->deletedAndSaved()->whereKey(2)->loadFirst();

// To restore it to a "Saved" state
$order->restore();
Order::save($order);

// To actually delete the model from the database use
$order->realDelete();
Order::save($order);

// To check if the model is soft deleted
echo $order->isSoftDeleted();
```
