<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Repo;
use Harp\Harp\Rel;


/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class PostTag extends AbstractModel {

    public static function initialize(Repo $repo)
    {
        $repo
            ->addRels([
                new Rel\BelongsTo('post', $repo, Post::getRepo()),
                new Rel\BelongsTo('tag', $repo, Tag::getRepo()),
            ]);
    }

    public $id;
    public $postId;
    public $tagId;

    public function getTag()
    {
        return $this->getLinkedModel('tag');
    }

    public function setTag(Tag $tag)
    {
        $this->setLinkedModel('tag', $tag);

        return $this;
    }

    public function getPost()
    {
        return $this->getLinkedModel('post');
    }

    public function setPost(Post $post)
    {
        $this->setLinkedModel('post', $post);

        return $this;
    }
}
