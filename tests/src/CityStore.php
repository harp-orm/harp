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
class CityStore extends Store {

    private static $instance;

    /**
     * @return CityStore
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new CityStore('CL\Luna\Test\City');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setRels([
                new Rel\HasMany('users', $this, UserStore::get()),
            ])
            ->setAsserts([
                new Assert\Present('location'),
            ])
            ->setFields([
                new Field\Integer('id'),
                new Field\String('zipCode'),
                new Field\String('location'),
            ]);
    }
}
