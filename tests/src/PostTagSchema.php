<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTagSchema extends Schema {

    use SchemaTrait;

    public function __construct()
    {
        parent::__construct('CL\Luna\Test\PostTag');
    }

    public function initialize()
    {
        $this
            ->setRels([
                new Rel\BelongsTo('post', $this, PostSchema::get()),
                new Rel\BelongsTo('tag', $this, TagSchema::get()),
            ])

            ->setFields([
                new Field\Integer('id'),
                new Field\Integer('postId'),
                new Field\Integer('tagId'),
            ]);
    }

}
