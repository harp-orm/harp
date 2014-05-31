<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;
use Harp\Harp\Field;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Country extends AbstractRepo {

    private static $instance;

    /**
     * @return Country
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Country('Harp\Harp\Test\Model\Country');
        }

        return self::$instance;
    }

    public function initialize()
    {

    }
}
