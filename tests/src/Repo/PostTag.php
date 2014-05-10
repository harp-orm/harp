<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\Model\Repo;

use CL\Luna\Field;
use CL\Luna\Rel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTag extends Repo {

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
            ->setRels([
                new Rel\BelongsTo('post', $this, Post::get()),
                new Rel\BelongsTo('tag', $this, Tag::get()),
            ])

            ->setFields([
                new Field\Integer('id'),
                new Field\Integer('postId'),
                new Field\Integer('tagId'),
            ]);
    }

}
