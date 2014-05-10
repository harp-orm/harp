<?php

namespace CL\Luna\Test\Model;

use CL\Luna\Model\Model;
use CL\Luna\Test\Store\CountryStore;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Country extends Model implements LocationInterface {

    public function getStore()
    {
        return CountryStore::get();
    }

    public $id;
    public $name;
}
