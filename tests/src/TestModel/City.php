<?php

namespace Harp\Harp\Test\TestModel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Rel;
use Harp\Validate\Assert;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class City extends AbstractModel implements LocationInterface {

    public static function initialize($config)
    {
        $config
            ->hasManyAs('users', __NAMESPACE__.'\User', 'location')
            ->belongsTo('country', __NAMESPACE__.'\Country')
            ->addAsserts([
                new Assert\Present('name'),
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
