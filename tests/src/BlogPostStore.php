<?php

namespace CL\Luna\Test;

use CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPostStore extends PostStore {

    private static $instance;

    /**
     * @return BlogPostStore
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new BlogPostStore('CL\Luna\Test\BlogPost');
        }

        return self::$instance;
    }

    public function initialize()
    {
        parent::initialize();

        $this
            ->setTable('Post')
            ->setFields([
                new Field\Boolean('isPublished'),
            ]);
    }

}
