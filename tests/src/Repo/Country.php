<?php

namespace Harp\Db\Test\Repo;

use Harp\Db\AbstractDbRepo;
use Harp\Db\Field;
use Harp\Db\Rel;
use Harp\Validate\Assert;

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
            self::$instance = new Country('Harp\Db\Test\Model\Country');
        }

        return self::$instance;
    }

    public function initialize()
    {

    }
}
