<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Schema;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class CountrySchema extends Schema {

    private static $instance;

    /**
     * @return CountrySchema
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new CountrySchema('CL\Luna\Test\Country');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }
}
