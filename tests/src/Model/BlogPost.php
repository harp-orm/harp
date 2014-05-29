<?php

namespace Harp\Db\Test\Model;

use Harp\Db\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPost extends Post {

    public function getRepo()
    {
        return Repo\BlogPost::get();
    }

    public $isPublished = false;

    public function getUser()
    {
        return Repo\BlogPost::get()->loadLink($this, 'user')->get();
    }

    public function getTags()
    {
        return Repo\BlogPost::get()->loadLink($this, 'tags');
    }

    public function getPostTags()
    {
        return Repo\BlogPost::get()->loadLink($this, 'postTags');
    }

    public function setUser(User $user)
    {
        return Repo\BlogPost::get()->loadLink($this, 'user')->set($user);
    }
}
