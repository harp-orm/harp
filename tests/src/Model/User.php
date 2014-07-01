<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class User extends AbstractModel {

    const REPO = 'Harp\Harp\Test\Repo\User';

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
        return $this->getLinkedModel('address');
    }

    public function setAddress(Address $address)
    {
        $this->setLinkedModel('address', $address);

        return $this;
    }

    public function getLocation()
    {
        return $this->getLinkedModel('location');
    }

    public function setLocation(LocationInterface $location)
    {
        $this->setLinkedModel('location', $location);

        return $this;
    }

    public function getProfile()
    {
        return $this->getLinkedModel('profile');
    }

    public function setProfile(Profile $profile)
    {
        $this->setLinkedModel('profile', $profile);

        return $this;
    }

    public function getPosts()
    {
        return $this->getLinkMany('posts');
    }
}
