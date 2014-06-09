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
class Post extends AbstractRepo {

    public static function newInstance()
    {
        return new Post('Harp\Harp\Test\Model\Post');
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

            ->addAsserts([
                new Assert\Present('title'),
            ]);
    }

}
