<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTag extends AbstractModel {

    const REPO = 'Harp\Harp\Test\Repo\PostTag';

    public $id;
    public $postId;
    public $tagId;

    public function getTag()
    {
        return $this->getLink('tag')->get();
    }

    public function setTag(Tag $tag)
    {
        $this->getLink('tag')->set($tag);

        return $this;
    }

    public function getPost()
    {
        return $this->getLink('post')->get();
    }

    public function setPost(Post $post)
    {
        $this->getLink('post')->set($post);

        return $this;
    }
}
