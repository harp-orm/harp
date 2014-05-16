<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\AbstractDbRepo;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

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
            self::$instance = new Post('CL\Luna\Test\Model\Post');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setPolymorphic(true)

            ->setRels([
                new Rel\BelongsTo('user', $this, User::get()),
                new Rel\HasMany('postTags', $this, PostTag::get()),
                new Rel\HasManyThrough('tags', $this, Tag::get(), 'postTags'),
            ])

            ->setFields([
                new Field\Integer('id'),
                new Field\String('title'),
                new Field\Text('body'),
                new Field\Decimal('price'),
                new Field\Serialized('tags', Field\Serialized::CSV),
                new Field\Timestamp('createdAt'),
                new Field\Timestamp('updatedAt'),
                new Field\DateTime('publishedAt'),
                new Field\Integer('userId'),
                new Field\String('polymorphicClass'),
            ])

            ->setAsserts([
                new Assert\Present('title'),
            ]);
    }

}
