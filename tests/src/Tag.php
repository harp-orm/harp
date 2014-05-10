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
class Tag extends Model {

    public function getStore()
    {
        return TagStore::get();
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
