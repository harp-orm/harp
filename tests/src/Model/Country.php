<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Test\Repo;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Country extends AbstractModel implements LocationInterface {

    const REPO = 'Harp\Harp\Test\Repo\Country';

    public $id;
    public $name;
}
