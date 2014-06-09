<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;
use Harp\Harp\Field;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Tag extends AbstractRepo {

    public static function newInstance()
    {
        return new Tag('Harp\Harp\Test\Model\Tag');
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\HasMany('postTags', $this, PostTag::get()),
                new Rel\HasManyThrough('posts', $this, Post::get(), 'postTags'),
            ])
            ->addAsserts([
                new Assert\Present('name'),
            ]);
    }

}
