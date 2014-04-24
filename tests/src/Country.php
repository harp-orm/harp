<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Country extends Model implements LocationInterface{

    use SchemaTrait;

    public $id;
    public $name;

    public static function initialize(Schema $schema)
    {
        $schema
            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }
}
