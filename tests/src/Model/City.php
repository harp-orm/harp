<?php

namespace CL\Luna\Test\Model;

use CL\Luna\Model\Model;
use CL\Luna\Test\Store\CityStore;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class City extends Model implements LocationInterface {

    public function getStore()
    {
        return CityStore::get();
    }

    public $id;
    public $name;
    public $countryId;

    public function getCountry()
    {
        return $this->loadRelLink('country')->get();
    }

    public function setCountry(Country $country)
    {
        return $this->loadRelLink('country')->set($country);
    }
}
