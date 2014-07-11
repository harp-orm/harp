<?php

namespace Harp\Harp\Model;

use Harp\Harp\Config;

/**
 * Add class property and methods to work with inheritence.
 * You need to call setInherited(true) on the corresponding repo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait InheritedTrait
{
    public static function initialize(Config $repo)
    {
        $repo->setInherited(true);
    }

    /**
     * @var string
     */
    public $class;
}
