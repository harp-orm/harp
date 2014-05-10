<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Store;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Address extends Model {

    public function getStore()
    {
        return AddressStore::get();
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
