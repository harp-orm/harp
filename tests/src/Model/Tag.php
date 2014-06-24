<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Tag extends AbstractModel {

    const REPO = 'Harp\Harp\Test\Repo\Tag';

    public $id;
    public $name;

    public function getPostTags()
    {
        return $this->getLink('postTags');
    }

    public function getPosts()
    {
        return $this->getLink('posts');
    }
}
