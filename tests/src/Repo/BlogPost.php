<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Field;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BlogPost extends Post {

    public function initialize()
    {
        parent::initialize();

        $this
            ->setModelClass('Harp\Harp\Test\Model\BlogPost')
            ->setRootRepo(Post::get());
    }

}
