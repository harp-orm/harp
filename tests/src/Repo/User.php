<?php

namespace Harp\Db\Test\Repo;

use Harp\Db\AbstractDbRepo;
use Harp\Db\Field;
use Harp\Db\Rel;
use Harp\Validate\Assert;

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
            self::$instance = new User('Harp\Db\Test\Model\User');
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

            ->initializeNestedRepo();
    }
}
