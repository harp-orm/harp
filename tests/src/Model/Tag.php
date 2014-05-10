<?php

namespace CL\Luna\Test\Model;

use CL\Luna\Model\Model;
use CL\Luna\Test\Repo;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Tag extends Model {

    public function getRepo()
    {
        return Repo\Tag::get();
    }

    public $id;
    public $name;

    /**
     * @return User
     */
    public function getPostTags()
    {
        return $this->loadRelLink('postTags');
    }

    /**
     * @return User
     */
    public function getPosts()
    {
        return $this->loadRelLink('posts');
    }
}
