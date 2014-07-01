<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class City extends AbstractModel implements LocationInterface {

    const REPO = 'Harp\Harp\Test\Repo\City';

    public $id;
    public $name;
    public $countryId;

    public function getCountry()
    {
        return $this->getLinkedModel('country');
    }

    public function setCountry(Country $country)
    {
        $this->setLinkedModel('country', $user);

        return $this;
    }
}
