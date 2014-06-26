# Repo

Configuration Method                   | Description
---------------------------------------|-------------
__setModelClass__($class)              | Set the corresponding model class, required. Must contain the namespace
__setTable__($table)                   | Set the name of the database table, defaults to the short class name of the model
__setDb__($dbName)                     | Set alternative database connection, this will tap into alternative database configurations you've setup
__setSoftDelete__($isSoftDelete)       | Set to true if you want this model to be soft deleted. More on [soft delete later](/docs/SoftDelete.md)
__setInherited__($isInherited)         | Set to true if this repo will be inherited by other repo using [Single table inheritance](/docs/Inherited.md)
__setRootRepo__(AbstractRepo $repo)    | Used for children in single table inheritence
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

## Custom Finders

If you want your Find objects to have custom methods, you can extend the ``findAll`` to return your own class and add your custom methods there:

```php
// Repo File
use Harp\Harp\AbstractRepo;

class OrderRepo extends AbstractRepo
{
    // ...
    public function findAll()
    {
        return new OrderFind($this);
    }
}

// OrderFind file
use Harp\Harp\Find;

class OrderFind extends Find
{
    public function whereEmail($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        return $this->where('email', $email);
    }
}

// In your code
$orders = Order::findAll()->whereEmail('some@example.com')->load();
```
