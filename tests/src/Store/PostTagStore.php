<?php

namespace CL\Luna\Test\Store;

use CL\Luna\Model\Store;

use CL\Luna\Field;
use CL\Luna\Rel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTagStore extends Store {

    private static $instance;

    /**
     * @return PostTagStore
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new PostTagStore('CL\Luna\Test\Model\PostTag');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setRels([
                new Rel\BelongsTo('post', $this, PostStore::get()),
                new Rel\BelongsTo('tag', $this, TagStore::get()),
            ])

            ->setFields([
                new Field\Integer('id'),
                new Field\Integer('postId'),
                new Field\Integer('tagId'),
            ]);
    }

}
