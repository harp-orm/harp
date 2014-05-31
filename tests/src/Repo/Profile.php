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
class Profile extends AbstractRepo {

    private static $instance;

    /**
     * @return Profile
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Profile('Harp\Harp\Test\Model\Profile');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\BelongsTo('user', $this, User::get()),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
