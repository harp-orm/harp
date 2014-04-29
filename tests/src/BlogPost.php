<?php

namespace CL\Luna\Test;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPost extends Post {

    public function getSchema()
    {
        return BlogPostSchema::get();
    }

    public $isPublished = false;
}
