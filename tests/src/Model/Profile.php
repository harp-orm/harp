<?php

namespace Harp\Harp\Test\Model;

use Harp\Harp\AbstractModel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Profile extends AbstractModel {

    const REPO = 'Harp\Harp\Test\Repo\Profile';

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var integer
     */
    public $userId;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->getLink('user')->get();
    }

    /**
     * @return User
     */
    public function setUser(User $user)
    {
        $this->getLink('user')->set($user);

        return $this;
    }
}
