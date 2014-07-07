<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BlogPost extends Post {

    public static function initialize(Repo $repo)
    {
        parent::initialize($repo);

        $repo
            ->setRootRepo(Post::getRepo());
    }

    public $isPublished = false;
}
