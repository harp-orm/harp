<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\AbstractDbRepo;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class User extends AbstractDbRepo {

    use NestedRepoTrait;

    private static $instance;

    /**
     * @return User
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new User('CL\Luna\Test\Model\User');
        }

        return self::$instance;
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

            ->setAsserts([
                new Assert\Present('name'),
            ])

            ->addEventAfterLoad(__CLASS__.'::test')

            ->initializeNestedRepo();
    }

    public static function test($model)
    {
        var_dump('User event "test" called');
    }

}
