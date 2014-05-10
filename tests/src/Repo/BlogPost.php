<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPost extends Post {

    private static $instance;

    /**
     * @return BlogPostRepo
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new BlogPost('CL\Luna\Test\Model\BlogPost');
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
