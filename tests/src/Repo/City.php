<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\AbstractRepo;
use Harp\Harp\Field;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class City extends AbstractRepo {

    public function initialize()
    {
        $this
            ->setModelClass('Harp\Harp\Test\Model\City')
            ->addRels([
                new Rel\HasManyAs('users', $this, User::get(), 'location'),
                new Rel\BelongsTo('country', $this, Country::get()),
            ])
            ->addAsserts([
                new Assert\Present('location'),
            ]);
    }
}
