<?php

namespace CL\Luna\Test\Model;

use CL\Luna\Model\Model;
use CL\Luna\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Address extends Model {

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
     * @return Users
     */
    public function getUsers()
    {
        return $this->loadRelLink('users');
    }
}