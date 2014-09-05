<?php

namespace Harp\Harp\Test\TestModel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Model\SoftDeleteTrait;
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

    public static function initialize($config)
    {
        SoftDeleteTrait::initialize($config);

        $config
            ->belongsTo('address', __NAMESPACE__.'\Address')
            ->belongsToPolymorphic('location', __NAMESPACE__.'\City')
            ->hasMany('posts', __NAMESPACE__.'\Post', [
                'inverseOf' => 'user',
                'linkClass' => __NAMESPACE__.'\LinkManyPosts'
            ])
            ->hasOne('profile', __NAMESPACE__.'\Profile', ['inverseOf' => 'user'])

            ->assertPresent('name')

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
