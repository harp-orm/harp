<?php

namespace Harp\Harp\Test\TestModel;

use Harp\Harp\AbstractModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class PostTag extends AbstractModel {

    public static function initialize($config)
    {
        $config
            ->belongsTo('post', __NAMESPACE__.'\Post')
            ->belongsTo('tag', __NAMESPACE__.'\Tag');
    }

    public $id;
    public $postId;
    public $tagId;

    public function getTag()
    {
        return $this->get('tag');
    }

    public function setTag(Tag $tag)
    {
        $this->set('tag', $tag);

        return $this;
    }

    public function getPost()
    {
        return $this->get('post');
    }

    public function setPost(Post $post)
    {
        $this->set('post', $post);

        return $this;
    }
}
