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
class Tag extends AbstractDbRepo {

    private static $instance;

    /**
     * @return TagRepo
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Tag('Harp\Db\Test\Model\Tag');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\HasMany('postTags', $this, PostTag::get()),
                new Rel\HasManyThrough('posts', $this, Post::get(), 'postTags'),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
