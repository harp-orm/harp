<?php

namespace Harp\Harp\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class User extends AbstractModel {

    public function getRepo()
    {
        return Repo\User::get();
    }

    use NestedTrait;

    public $id;
    public $name;
    public $password;
    public $addressId;
    public $isBlocked = false;
    public $deletedAt;
    public $locationId;
    public $locationClass;
    public $test;

    public function getAddress()
    {
        return Repo\User::get()->loadLink($this, 'address')->get();
    }

    public function setAddress(Address $address)
    {
        return Repo\User::get()->loadLink($this, 'address')->set($address);
    }

    public function getLocation()
    {
        return Repo\User::get()->loadLink($this, 'location')->get();
    }

    public function setLocation(LocationInterface $location)
    {
        return Repo\User::get()->loadLink($this, 'location')->set($location);
    }

    public function getProfile()
    {
        return Repo\User::get()->loadLink($this, 'profile')->get();
    }

    public function setProfile(Profile $profile)
    {
        return Repo\User::get()->loadLink($this, 'profile')->set($profile);
    }

    public function getPosts()
    {
        return Repo\User::get()->loadLink($this, 'posts');
    }
}
