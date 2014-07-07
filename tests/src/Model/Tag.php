<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Repo;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Tag extends AbstractModel {

    public static function initialize(Repo $repo)
    {
        $repo
            ->addRels([
                new Rel\HasMany('postTags', $repo, PostTag::getRepo()),
                new Rel\HasManyThrough('posts', $repo, Post::getRepo(), 'postTags'),
            ])
            ->addAsserts([
                new Assert\Present('name'),
            ]);
    }

    public $id;
    public $name;

    public function getPostTags()
    {
        return $this->getLinkMany('postTags');
    }

    public function getPosts()
    {
        return $this->getLinkMany('posts');
    }
}
