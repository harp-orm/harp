<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Address extends AbstractModel {

    public function getRepo()
    {
        return Repo\Address::get();
    }

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $zipCode;

    /**
     * @var string
     */
    public $location;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->getLink('user')->get();
    }

    public function setUser(User $user)
    {
        $this->getLink('user')->set($user);

        return $this;
    }
}
