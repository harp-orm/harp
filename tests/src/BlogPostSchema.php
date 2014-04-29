<?php

namespace CL\Luna\Test;

use CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPostSchema extends PostSchema {

    private static $instance;

    /**
     * @return BlogPostSchema
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new BlogPostSchema('CL\Luna\Test\BlogPost');
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
