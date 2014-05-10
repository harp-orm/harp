<?php

namespace CL\Luna\Test\Store;

use CL\Luna\Model\Store;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostStore extends Store {

    private static $instance;

    /**
     * @return PostStore
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new PostStore('CL\Luna\Test\Model\Post');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setPolymorphic(true)

            ->setRels([
                new Rel\BelongsTo('user', $this, UserStore::get()),
                new Rel\HasMany('postTags', $this, PostTagStore::get()),
                new Rel\HasManyThrough('tags', $this, TagStore::get(), 'postTags'),
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
