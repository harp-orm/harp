<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Test\Repo;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Profile extends AbstractModel {

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
        return $this->getLink('user')->get();
    }

    /**
     * @return User
     */
    public function setUser(User $user)
    {
        $this->getLink('user')->set($user);

        return $this;
    }
}
