<?php

namespace CL\Luna\Test\Model;

use CL\Luna\Model\Model;
use CL\Luna\Test\Store\PostStore;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Post extends Model {

    public function getStore()
    {
        return PostStore::get();
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
    public $polymorphicClass;

    public function getUser()
    {
        return $this->loadRelLink('user')->get();
    }

    public function getTags()
    {
        return $this->loadRelLink('tags');
    }

    public function getPostTags()
    {
        return $this->loadRelLink('postTags');
    }

    public function setUser(User $user)
    {
        return $this->loadRelLink('user')->set($user);
    }
}
