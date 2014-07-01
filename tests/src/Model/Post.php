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
        return $this->getLinkedModel('user');
    }

    public function getTags()
    {
        return $this->getLinkMany('tags');
    }

    public function getPostTags()
    {
        return $this->getLinkMany('postTags');
    }

    public function setUser(User $user)
    {
        $this->getLinkedModel('user', $user);

        return $this;
    }
}
