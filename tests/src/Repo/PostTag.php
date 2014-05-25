<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\AbstractDbRepo;

use CL\Luna\Field;
use CL\Luna\Rel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTag extends AbstractDbRepo {

    private static $instance;

    /**
     * @return PostTagRepo
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new PostTag('CL\Luna\Test\Model\PostTag');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\BelongsTo('post', $this, Post::get()),
                new Rel\BelongsTo('tag', $this, Tag::get()),
            ]);
    }

}
