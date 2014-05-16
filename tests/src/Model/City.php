<?php

namespace CL\Luna\Test\Model;

use CL\Luna\AbstractDbModel;
use CL\Luna\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class City extends AbstractDbModel implements LocationInterface {

    public function getRepo()
    {
        return Repo\City::get();
    }

    public $id;
    public $name;
    public $countryId;

    public function getCountry()
    {
        return Repo\City::get()->loadLink($this, 'country')->get();
    }

    public function setCountry(Country $country)
    {
        return Repo\City::get()->loadLink($this, 'country')->set($country);
    }
}
