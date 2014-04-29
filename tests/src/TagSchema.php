<?php namespace CL\Luna\Test;

use CL\Luna\Model\Schema;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class TagSchema extends Schema {

    private static $instance;

    /**
     * @return TagSchema
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new TagSchema('CL\Luna\Test\Tag');
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
                new Rel\HasMany('postTags', $this, PostTagSchema::get()),
                new Rel\HasManyThrough('posts', $this, PostSchema::get(), 'postTags'),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
