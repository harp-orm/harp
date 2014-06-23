<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;
use Harp\Core\Model\AbstractModel;
use Harp\Harp\Field;
use Harp\Harp\Rel;
use Harp\Validate\Assert;
use Harp\Serializer;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class User extends AbstractRepo {

    use NestedRepoTrait;

    public function initialize()
    {
        $this
            ->setModelClass('Harp\Harp\Test\Model\User')

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

            ->addSerializers([
                new Serializer\Native('object')
            ])

            ->initializeNestedRepo();
    }
}
