<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BlogPost extends Post {

    const REPO = 'Harp\Harp\Test\Repo\BlogPost';

    public $isPublished = false;
}
