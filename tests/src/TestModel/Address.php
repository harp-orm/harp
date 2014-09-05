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
class Address extends AbstractModel {

    public static function initialize($config)
    {
        $config
            ->hasOne('user', __NAMESPACE__.'\User')
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
