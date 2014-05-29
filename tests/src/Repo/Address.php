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
class Address extends AbstractDbRepo {

    private static $instance;

    /**
     * @return PostRepo
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Address('Harp\Db\Test\Model\Address');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\HasOne('user', $this, User::get()),
            ])
            ->setAsserts([
                new Assert\Present('location'),
            ]);
    }
}
