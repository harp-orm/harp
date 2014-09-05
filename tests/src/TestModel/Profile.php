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
class Profile extends AbstractModel {

    public static function initialize($config)
    {
        $config
            ->belongsTo('user', __NAMESPACE__.'\User', ['inverseOf' => 'profile'])
            ->addAsserts([
                new Assert\Present('name'),
            ]);
    }

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
        return $this->get('user');
    }

    /**
     * @return User
     */
    public function setUser(User $user)
    {
        $this->set('user', $user);

        return $this;
    }
}
