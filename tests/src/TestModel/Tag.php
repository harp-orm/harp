<?php

namespace Harp\Harp\Test\TestModel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Tag extends AbstractModel {

    public static function initialize($config)
    {
        $config
            ->addRels([
                new Rel\HasMany('postTags', $config, PostTag::getRepo()),
                new Rel\HasManyThrough('posts', $config, Post::getRepo(), 'postTags'),
            ])
            ->addAsserts([
                new Assert\Present('name'),
            ]);
    }

    public $id;
    public $name;

    public function getPostTags()
    {
        return $this->all('postTags');
    }

    public function getPosts()
    {
        return $this->all('posts');
    }
}
