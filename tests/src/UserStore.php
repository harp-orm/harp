<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Store;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class UserStore extends Store {

    use NestedStoreTrait;

    private static $instance;

    /**
     * @return UserStore
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new UserStore('CL\Luna\Test\User');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setSoftDelete(true)

            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
                new Field\Password('password'),
                new Field\Boolean('isBlocked'),
                new Field\Integer('addressId'),
                new Field\Integer('locationId'),
                new Field\String('locationClass'),
                new Field\Timestamp('deletedAt'),
            ])

            ->setRels([
                new Rel\BelongsTo('address', $this, AddressStore::get()),
                new Rel\BelongsToPolymorphic('location', $this, CityStore::get()),
                new Rel\HasMany('posts', $this, PostStore::get()),
                new Rel\HasOne('profile', $this, ProfileStore::get()),
            ])

            ->setAsserts([
                new Assert\Present('name'),
            ])

            ->setEventAfterLoad('CL\Luna\Test\UserStore::test');
    }

    public static function test($model)
    {
        var_dump('User event "test" called');
    }

}
