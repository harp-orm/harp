<?php

namespace Harp\Harp\Test\Unit;

use Harp\Harp\AbstractRepo;
use Harp\Harp\AbstractModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ModelMock extends AbstractModel {

    private static $repo;

    public static function setRepoStatic(AbstractRepo $repo)
    {
        static::$repo = $repo;
    }

    public static function getRepoStatic()
    {
        return static::$repo;
    }
}
