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
class Profile extends AbstractModel {

    public static function initialize(Repo $repo)
    {
        $repo
            ->addRels([
                new Rel\BelongsTo('user', $repo, User::getRepo()),
            ])
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
        return $this->getLinkedModel('user');
    }

    /**
     * @return User
     */
    public function setUser(User $user)
    {
        $this->getLinkedModel('user', $user);

        return $this;
    }
}
