<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;
use Harp\Core\Model\AbstractModel;
use Harp\Harp\Field;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class User extends AbstractRepo {

    use NestedRepoTrait;

    public static function newInstance()
    {
        return new User('Harp\Harp\Test\Model\User');
    }

    public function initialize()
    {
        $this
            ->setSoftDelete(true)

            ->addRels([
                new Rel\BelongsTo('address', $this, Address::get()),
                new Rel\BelongsToPolymorphic('location', $this, City::get()),
                new Rel\HasMany('posts', $this, Post::get()),
                new Rel\HasOne('profile', $this, Profile::get()),
            ])

            ->addAsserts([
                new Assert\Present('name'),
            ])

            ->initializeNestedRepo();
    }

    public function unserializeModel(AbstractModel $model)
    {
        if (is_string($model->object)) {
            $model->object = unserialize($model->object);
        }

        return $this;
    }

    public function serializeModel(array $properties)
    {
        if (isset($properties['object'])) {
            $properties['object'] = serialize($properties['object']);
        }

        return $properties;
    }
}
