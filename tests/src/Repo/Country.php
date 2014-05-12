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
class Country extends AbstractDbRepo {

    private static $instance;

    /**
     * @return Country
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Country('CL\Luna\Test\Model\Country');
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
