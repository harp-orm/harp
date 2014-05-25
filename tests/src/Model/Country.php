<?php

namespace CL\Luna\Test\Model;

use CL\LunaCore\Model\AbstractModel;
use CL\Luna\Test\Repo;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Country extends AbstractModel implements LocationInterface {

    public function getRepo()
    {
        return Repo\Country::get();
    }

    public $id;
    public $name;
}
