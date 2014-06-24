<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Core\Model\InheritedTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Post extends AbstractModel {

    const REPO = 'Harp\Harp\Test\Repo\Post';

    use InheritedTrait;

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
        return $this->getLink('user')->get();
    }

    public function getTags()
    {
        return $this->getLink('tags');
    }

    public function getPostTags()
    {
        return $this->getLink('postTags');
    }

    public function setUser(User $user)
    {
        $this->getLink('user')->set($user);

        return $this;
    }
}
