<?php

namespace Harp\Harp\Test\TestModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BlogPost extends Post {

    public static function initialize($config)
    {
        parent::initialize($config);
    }

    public $isPublished = false;
}
