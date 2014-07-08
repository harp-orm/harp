# Extending

Generally extending is accomplished with [PHP's native Traits](http://www.php.net/manual/en/language.oop5.traits.php). You can also add functionality to be called on event trigger e.g. "save", "delete" etc. And you can add your own methods to the ``Find`` classes, by extending it.


# Extending with traits

A quick naive implementation of slugs.

```php
// Extending the model
trait ExtensionTrait
{
    // Add a property to the model
    // By default all public properties are saved in the database
    public $slug;

    // This will help the static analyzers as well as force this trait to only be added to models
    abstract public static getRepoStatic();

    // adding a new function to search with from the model
    public static function findBySlug($slug)
    {
        // static keyword will refer to the class this trait will be extending
        return static::getRepoStatic()->findAll()->where('name', $slug)->loadFirst();
    }

    // add a method to the model itself
    public function updateSlug()
    {
        $this->slug = $this->id.$this->name;
    }
}

// Extending the repo
trait ExtensionRepoTrait
{
    // Add a uniquely named initialize method to be called from the repo initialize
    public function initializeExtension()
    {
        // You will be able to configure the repo however you want here,
        // since you have access to all the repo configuration setters.
        $this->addEventAfter(Event::CONSTRUCT, function ($model) {
            $model->updateSlug();
        });
    }
}

// In the model
class User extends AbstractModel
{
    use ExtensionTrait;

    // ...
}

// In the repo
class UserRepo extends AbstractRepo
{
    use ExtensionRepoTrait;

    public function initialize()
    {
        // Call the initialize method from the repo triat.
        $this->initializeExtension();

        // ...
    }
}
```

## Using events

You can subscribe to several events that get triggered in the life-cycle of models. All of these events are in the Harp\Core\Repo\Event class:

Event            | Action
-----------------|-----------------
Event::CONSTRUCT | Called after the constructor of the model has been executed
Event::INSERT    | Called when the model is being inserted in the database
Event::UPDATE    | Called when an already saved model is being updated
Event::DELETE    | Called when a model is being deleted from the database. Works for normal and soft delete
Event::SAVE      | Called either on INSERT or UPDATE
Event::VALIDATE  | Called when a model is being validated. E.g. the "validate" method is being called. This is useful to add some custom validation before / after

All of these events have a "before" and "after" phase. Except "CONSTRUCT", which only has "after".

Here's an example:

```php
// In the repo
class UserRepo extends AbstractRepo
{
    public function initialize()
    {
        $this
            ->addEventAfter(Event::SAVE, 'Test\Class::myMethod')
            ->addEventBefore(Event::DELETE, function ($model) {
                $model->cleanUpDelete();
            });
    }
}
```

## Extending Find

The ``Find`` class has a variety of methods for adding sql constraints. Methods like ``where``, ``join``, ``group``, ``order`` etc. Sometimes you'll want to add your own methods there, specific to the repo. To do this you need to create your own class, extending ``Find``, and modify the "findAll" method on the model to return your own class.


```php
// In the repo
use Harp\Harp\AbstractRepo;

class User extends AbstractModel
{
    // ...

    public static function findAll()
    {
        return new MyFind(self::getRepo());
    }
}

// Find class
use Harp\Harp\Find;

class MyFind extends Find
{
    public function whereSlug($slug)
    {
        return $this->where('slug', $slug);
    }
}
```

