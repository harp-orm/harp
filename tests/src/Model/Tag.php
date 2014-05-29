<?php

namespace Harp\Db\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Db\Test\Repo;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Tag extends AbstractModel {

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
        return Repo\Tag::get()->loadLink($this, 'postTags');
    }

    /**
     * @return User
     */
    public function getPosts()
    {
        return Repo\Tag::get()->loadLink($this, 'posts');
    }
}
