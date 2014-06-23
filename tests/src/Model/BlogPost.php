<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPost extends Post {

    const REPO = 'Harp\Harp\Test\Repo\BlogPost';

    public $isPublished = false;
}
