<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;
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

            ->setAsserts([
                new Assert\Present('name'),
            ])

            ->initializeNestedRepo();
    }
}
