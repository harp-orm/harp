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
        return Repo\Post::get()->loadLink($this, 'user')->get();
    }

    public function getTags()
    {
        return Repo\Post::get()->loadLink($this, 'tags');
    }

    public function getPostTags()
    {
        return Repo\Post::get()->loadLink($this, 'postTags');
    }

    public function setUser(User $user)
    {
        return Repo\Post::get()->loadLink($this, 'user')->set($user);
    }
}
