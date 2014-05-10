<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Store;
use CL\Luna\Model\StoreTrait;
use CL\Luna\Field;
use CL\Carpo\Assert;

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
