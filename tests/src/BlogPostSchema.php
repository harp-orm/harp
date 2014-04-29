<?php

namespace CL\Luna\Test;

use CL\Luna\Field;
use CL\Luna\Model\SchemaTrait;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPostSchema extends PostSchema {

    use SchemaTrait;

    public function __construct()
    {
        parent::__construct('CL\Luna\Test\BlogPost');
    }

    public function initialize()
    {
        parent::initialize();

        $this
            ->setTable('Post')
            ->setFields([
                new Field\Boolean('isPublished'),
            ]);
    }

}
