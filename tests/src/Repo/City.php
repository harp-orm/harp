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
class City extends AbstractDbRepo {

    private static $instance;

    /**
     * @return CityRepo
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new City('Harp\Db\Test\Model\City');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\HasMany('users', $this, User::get()),
                new Rel\BelongsTo('country', $this, Country::get()),
            ])
            ->setAsserts([
                new Assert\Present('location'),
            ]);
    }
}
