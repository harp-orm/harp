# Extending

Generally extending is accomplished with [PHP's native Traits](http://www.php.net/manual/en/language.oop5.traits.php). You can also add functionality to be called on event trigger e.g. "save", "delete" etc. And you can add your own methods to the ``Find`` classes, by extending it.

# Extending with traits

A quick naive implementation of slugs.

```php
use Harp\Harp\Repo\Event;

trait ExtensionTrait
{
    public static function initialize($config)
    {
        // Here you can work with the config object as if you're in the model's initialize method itself
        $config->addEventAfter(Event::CONSTRUCT, function ($model) {
            $model->updateSlug();
        });
    }

    // Add a property to the model
    // By default all public properties are saved in the database
    public $slug;

    // adding a new function to search with from the model
    public static function findBySlug($slug)
    {
        // static keyword will refer to the class this trait will be extending
        return static::where('name', $slug)->loadFirst();
    }

    // add a method to the model itself
    public function updateSlug()
    {
        $this->slug = $this->id.$this->name;
    }
}

// In the model
class User extends AbstractModel
{
    use ExtensionTrait;

    public static function initialize($config)
    {
        ExtensionTrait::initialize($config);

        // ...
    }
}
```

## Using events

You can subscribe to several events that get triggered in the life-cycle of models. All of these events are in the Harp\Harp\Repo\Event class:

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
use Harp\Harp\AbstractModel;
use Harp\Harp\Repo\Event;

class User extends AbstractModel
{
    public static function initialize($config)
    {
        $this
            ->addEventAfter(Event::SAVE, function ($model) {
                $model->preseveSave();
            })
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
use Harp\Harp\AbstractModel;

class User extends AbstractModel
{
    // ...

    public static function findAll()
    {
        return new MyFind(static::getRepo());
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

