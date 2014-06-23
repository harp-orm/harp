<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;

use Harp\Harp\Field;
use Harp\Harp\Rel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTag extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Harp\Harp\Test\Model\PostTag')
            ->addRels([
                new Rel\BelongsTo('post', $this, Post::get()),
                new Rel\BelongsTo('tag', $this, Tag::get()),
            ]);
    }

}
