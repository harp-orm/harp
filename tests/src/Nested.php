<?php namespace CL\Luna\Test;

use CL\Luna\Model\Schema;
use CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait Nested {

    /**
     * @var string
     */
    public $parentId;

    public static function initialize(Schema $schema)
    {
        $schema
            ->getFields()
                ->add(new Field\Integer('parentId'));
    }

    /**
     * @event save
     */
    public function applyNested()
    {
        echo 'do stuff';
    }
}
