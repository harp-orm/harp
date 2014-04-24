<?php namespace CL\Luna\Test;

use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;
use CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BlogPost extends Post {

    use SchemaTrait;

    public $isPublished = false;

    public static function initialize(Schema $schema)
    {
        parent::initialize($schema);

        $schema
            ->setFields([
                new Field\Boolean('isPublished'),
            ]);
    }

}
