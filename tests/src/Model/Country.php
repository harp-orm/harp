<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;
use Harp\Harp\Rel;
use Harp\Harp\Repo;
/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Country extends AbstractModel implements LocationInterface {

    public static function initialize(Repo $repo)
    {
        $repo
            ->addRels([
                new Rel\HasManyAs('users', $repo, User::getRepo(), 'location'),
            ]);
    }

    public $id;
    public $name;
}
