<?php

namespace CL\Luna\Test\Model;

use CL\Luna\Model\Model;
use CL\Luna\Test\Repo;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Profile extends Model {

    public function getRepo()
    {
        return Repo\Profile::get();
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
