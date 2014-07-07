<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Repo;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class City extends AbstractModel implements LocationInterface {

    public static function initialize(Repo $repo)
    {
        $repo
            ->addRels([
                new Rel\HasManyAs('users', $repo, User::getRepo(), 'location'),
                new Rel\BelongsTo('country', $repo, Country::getRepo()),
            ])
            ->addAsserts([
                new Assert\Present('location'),
            ]);
    }

    public $id;
    public $name;
    public $countryId;

    public function getCountry()
    {
        return $this->get('country');
    }

    public function setCountry(Country $country)
    {
        $this->set('country', $country);

        return $this;
    }
}
