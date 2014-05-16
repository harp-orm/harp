<?php

namespace CL\Luna\Test\Model;

use CL\Luna\AbstractDbModel;
use CL\Luna\Test\Repo;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Country extends AbstractDbModel implements LocationInterface {

    public function getRepo()
    {
        return Repo\Country::get();
    }

    public $id;
    public $name;
}
