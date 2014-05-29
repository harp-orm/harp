<?php

namespace Harp\Db\Test\Repo;

use Harp\Db\AbstractDbRepo;
use Harp\Db\Field;
use Harp\Db\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Post extends AbstractDbRepo {

    private static $instance;

    /**
     * @return Post
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Post('Harp\Db\Test\Model\Post');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setInherited(true)

            ->addRels([
                new Rel\BelongsTo('user', $this, User::get()),
                new Rel\HasMany('postTags', $this, PostTag::get()),
                new Rel\HasManyThrough('tags', $this, Tag::get(), 'postTags'),
            ])

            ->setAsserts([
                new Assert\Present('title'),
            ]);
    }

}
