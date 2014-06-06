<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPost extends Post {

    public static function newInstance()
    {
        return new BlogPost('Harp\Harp\Test\Model\BlogPost');
    }

    public function initialize()
    {
        parent::initialize();

        $this
            ->setRootRepo(Post::get());
    }

}
