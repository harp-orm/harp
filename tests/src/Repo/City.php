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
class City extends AbstractRepo {

    public static function newInstance()
    {
        return new City('Harp\Harp\Test\Model\City');
    }

    public function initialize()
    {
        $this
            ->addRels([
                new Rel\HasMany('users', $this, User::get()),
                new Rel\BelongsTo('country', $this, Country::get()),
            ])
            ->addAsserts([
                new Assert\Present('location'),
            ]);
    }
}
