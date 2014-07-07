<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Repo;
use Harp\Core\Model\SoftDeleteTrait;
use Harp\Harp\Rel;
use Harp\Validate\Assert;
use Harp\Serializer;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class User extends AbstractModel
{
    use NestedTrait;
    use SoftDeleteTrait;

    public static function initialize(Repo $repo)
    {
        SoftDeleteTrait::initialize($repo);

        $repo
            ->addRels([
                new Rel\BelongsTo('address', $repo, Address::getRepo()),
                new Rel\BelongsToPolymorphic('location', $repo, City::getRepo()),
                new Rel\HasMany('posts', $repo, Post::getRepo()),
                new Rel\HasOne('profile', $repo, Profile::getRepo()),
            ])

            ->addAsserts([
                new Assert\Present('name'),
            ])

            ->addSerializers([
                new Serializer\Native('object')
            ]);
    }

    public $id;
    public $name;
    public $password;
    public $addressId;
    public $isBlocked = false;
    public $locationId;
    public $locationClass;
    public $test;
    public $object;

    public function getAddress()
    {
        return $this->get('address');
    }

    public function setAddress(Address $address)
    {
        $this->set('address', $address);

        return $this;
    }

    public function getLocation()
    {
        return $this->get('location');
    }

    public function setLocation(LocationInterface $location)
    {
        $this->set('location', $location);

        return $this;
    }

    public function getProfile()
    {
        return $this->get('profile');
    }

    public function setProfile(Profile $profile)
    {
        $this->set('profile', $profile);

        return $this;
    }

    public function getPosts()
    {
        return $this->all('posts');
    }
}
