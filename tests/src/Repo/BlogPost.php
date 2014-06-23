<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
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
