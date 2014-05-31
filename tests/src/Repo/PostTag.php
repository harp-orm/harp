<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractDbRepo;

use Harp\Harp\Field;
use Harp\Harp\Rel;

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
            self::$instance = new PostTag('Harp\Harp\Test\Model\PostTag');
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
