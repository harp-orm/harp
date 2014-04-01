<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;
use CL\Luna\Model\ModelEvent;
use CL\Luna\Repo\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Profile extends Model {

    use SchemaTrait;

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
        return Repo::getLink($this, 'user')->get();
    }

    /**
     * @return User
     */
    public function setUser(User $user)
    {
        return Repo::getLink($this, 'user')->set($user);
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setFields([
                new Field\Integer('id'),
                new Field\String('firstName'),
                new Field\String('lastName'),
            ])
            ->setRels([
                new Rel\BelongsTo('user', User::getSchema()),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
