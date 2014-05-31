<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Field;

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
            self::$instance = new BlogPost('Harp\Harp\Test\Model\BlogPost');
        }

        return self::$instance;
    }

    public function initialize()
    {
        parent::initialize();

        $this
            ->setTable('Post');
    }

}
