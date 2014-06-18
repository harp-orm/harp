<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
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
    public $object;

    public function getAddress()
    {
        return $this->getLink('address')->get();
    }

    public function setAddress(Address $address)
    {
        $this->getLink('address')->set($address);

        return $this;
    }

    public function getLocation()
    {
        return $this->getLink('location')->get();
    }

    public function setLocation(LocationInterface $location)
    {
        $this->getLink('location')->set($location);

        return $this;
    }

    public function getProfile()
    {
        return $this->getLink('profile')->get();
    }

    public function setProfile(Profile $profile)
    {
        $this->getLink('profile')->set($profile);

        return $this;
    }

    public function getPosts()
    {
        return $this->getLink('posts');
    }
}
