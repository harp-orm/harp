<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Repo;
use Harp\Core\Model\InheritedTrait;
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

    public static function initialize(Repo $repo)
    {
        InheritedTrait::initialize($repo);

        $repo
            ->addRels([
                new Rel\BelongsTo('user', $repo, User::getRepo()),
                new Rel\HasMany('postTags', $repo, PostTag::getRepo()),
                new Rel\HasManyThrough('tags', $repo, Tag::getRepo(), 'postTags'),
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
