<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTag extends Model {

    public function getStore()
    {
        return PostTagStore::get();
    }

    public $id;
    public $postId;
    public $tagId;

    public function getTag()
    {
        return $this->loadRelLink('tag')->get();
    }

    public function setTag(Tag $tag)
    {
        return $this->loadRelLink('tag')->set($tag);
    }

    public function getPost()
    {
        return $this->loadRelLink('post')->get();
    }

    public function setPost(Post $post)
    {
        return $this->loadRelLink('post')->set($post);
    }
}
