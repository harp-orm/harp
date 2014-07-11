<?php

namespace Harp\Harp\Test\TestModel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Rel;
use Harp\Harp\Repo;
/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Country extends AbstractModel implements LocationInterface {

    public static function initialize($config)
    {
        $config
            ->addRels([
                new Rel\HasManyAs('users', $config, User::getRepo(), 'location'),
                new Rel\HasMany('cities', $config, City::getRepo()),
            ]);
    }

    public $id;
    public $name;
}
