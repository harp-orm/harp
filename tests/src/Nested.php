<?php namespace CL\Luna\Test;

use CL\Luna\Schema\Schema;
use CL\Luna\Field\Integer;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait Nested {

    /**
     * @var string
     */
    public $parent;

    public static function CL_Luna_Test_Nested(Schema $schema)
    {
        $schema
            ->getFields()
                ->add(new Integer('parent_id'));
    }

    /**
     * @event save
     */
    public function applyNested()
    {
        echo 'do stuff';
    }
}
