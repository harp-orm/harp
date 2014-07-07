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
class Address extends AbstractModel {

    public static function initialize(Repo $repo)
    {
        $repo
            ->addRels([
                new Rel\HasOne('user', $repo, User::getRepo()),
            ])
            ->addAsserts([
                new Assert\Present('location'),
            ]);
    }

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $zipCode;

    /**
     * @var string
     */
    public $location;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->get('user');
    }

    public function setUser(User $user)
    {
        $this->set('user', $user);

        return $this;
    }
}
