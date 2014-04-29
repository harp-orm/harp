<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Post extends Model {

    public function getSchema()
    {
        return PostSchema::get();
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
