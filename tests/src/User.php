<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class User extends Model {

    public function getStore()
    {
        return UserStore::get();
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
        return $this->loadRelLink('address')->get();
    }

    public function setAddress(Address $address)
    {
        return $this->loadRelLink('address')->set($address);
    }

    public function getLocation()
    {
        return $this->loadRelLink('location')->get();
    }

    public function setLocation(LocationInterface $location)
    {
        return $this->loadRelLink('location')->set($location);
    }

    public function getProfile()
    {
        return $this->loadRelLink('profile')->get();
    }

    public function setProfile(Profile $profile)
    {
        return $this->loadRelLink('profile')->set($profile);
    }

    public function getPosts()
    {
        return $this->loadRelLink('posts');
    }
}
