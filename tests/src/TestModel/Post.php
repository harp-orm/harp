<?php

namespace Harp\Harp\Test\TestModel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Model\InheritedTrait;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Post extends AbstractModel
{
    use InheritedTrait;

    public static function initialize($config)
    {
        InheritedTrait::initialize($config);

        $config
            ->addRels([
                new Rel\BelongsTo('user', $config, User::getRepo()),
                new Rel\HasMany('postTags', $config, PostTag::getRepo()),
                new Rel\HasManyThrough('tags', $config, Tag::getRepo(), 'postTags'),
            ])
            ->addAsserts([
                new Assert\Present('title'),
            ]);
    }

    public $id;
    public $title;
    public $body;
    public $price;
    public $tags;
    public $createdAt;
    public $updatedAt;
    public $publishedAt;
    public $userId;

    public function getUser()
    {
        return $this->get('user');
    }

    public function getTags()
    {
        return $this->all('tags');
    }

    public function getPostTags()
    {
        return $this->all('postTags');
    }

    public function setUser(User $user)
    {
        $this->set('user', $user);

        return $this;
    }
}
