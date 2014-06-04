<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Post extends AbstractModel {

    public function getRepo()
    {
        return Repo\Post::get();
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
    public $class;

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
