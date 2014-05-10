<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Store;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class CountryStore extends Store {

    private static $instance;

    /**
     * @return CountryStore
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new CountryStore('CL\Luna\Test\Country');
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
