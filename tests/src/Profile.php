<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Store;
use CL\Luna\Model\StoreTrait;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Profile extends Model {

    public function getStore()
    {
        return ProfileStore::get();
    }

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var integer
     */
    public $userId;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->loadRelLink('user')->get();
    }

    /**
     * @return User
     */
    public function setUser(User $user)
    {
        return $this->loadRelLink('user')->set($user);
    }
}
