# Assertions

Assertions are objects that make sure object's properties are valid in defined parameters. It uses [Harp\Validate](https://github.com/harp-orm/validate) for all the assert classes, but some packages / extensions add their own, and you can sertainly write them yourself.

Here's a quick example how to use assertions:

```php
use Harp\Harp\AbstractModel;
use Harp\Validate\Assert;

class Post extends AbstractModel
{
    public static function initialize($config)
    {
        $this->addAsserts([

            // Validate that the model will have a name property
            new Assert\Present('name'),

            // Validate that the model will have a name property
            // You can pass the text of the actual error message
            new Assert\Present('body', 'Must have text here'),

            // Validate the body has the size we want
            new Assert\LengthBetween('body', 10, 1000),
        ]);
    }

    public $id;
    public $name;
    public $body;
}

$post = new Post();

// To execute all the assertions, call "validate"
echo $post->validate(); // Will return false
echo $post->getErrors(); // Will return all the errors, as a single string

// You can iterate over the errors in a foreach
foreach ($post->getErrors() as $error) {
    echo $error->getName();
    echo $error->getFullMessage();
}

// To get the result of the last validate use isEmptyErrors
echo $post->isEmptyErrors();

// If you attempt to save a model that is not valid, a Harp\Harp\InvalidModelException will be thrown
Post::save($post);

$post->name = 'News';
$post->body = 'Today, there are several breaking news';
echo $post->validate(); // Will return true
```

You can read about assertions in depth from the [Harp\Validate's README](https://github.com/harp-orm/validate)
