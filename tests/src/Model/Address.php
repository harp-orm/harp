<?php

namespace Harp\Db\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Db\Test\Repo;

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
        return Repo\Address::get()->loadLink($this, 'user')->get();
    }

    public function setUser(User $user)
    {
        Repo\Address::get()->loadLink($this, 'user')->set($user);

        return $this;
    }
}
