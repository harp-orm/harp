<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class City extends AbstractModel implements LocationInterface {

    public function getRepo()
    {
        return Repo\City::get();
    }

    public $id;
    public $name;
    public $countryId;

    public function getCountry()
    {
        return $this->getLink('country')->get();
    }

    public function setCountry(Country $country)
    {
        $this->getLink('country')->set($country);

        return $this;
    }
}
