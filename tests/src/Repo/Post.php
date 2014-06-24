<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;
use Harp\Harp\Field;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Post extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setInherited(true)

            ->setModelClass('Harp\Harp\Test\Model\Post')

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
