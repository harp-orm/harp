<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Test\Repo;
/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Country extends AbstractModel implements LocationInterface {

    const REPO = 'Harp\Harp\Test\Repo\Country';

    public $id;
    public $name;
}
