<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\Model\AbstractDbRepo;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class City extends AbstractDbRepo {

    private static $instance;

    /**
     * @return CityRepo
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new City('CL\Luna\Test\Model\City');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setRels([
                new Rel\HasMany('users', $this, User::get()),
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
