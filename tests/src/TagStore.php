<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Store;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class TagStore extends Store {

    private static $instance;

    /**
     * @return TagStore
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new TagStore('CL\Luna\Test\Tag');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
            ])
            ->setRels([
                new Rel\HasMany('postTags', $this, PostTagStore::get()),
                new Rel\HasManyThrough('posts', $this, PostStore::get(), 'postTags'),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
