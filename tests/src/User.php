<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;
use CL\Luna\Mapper\NodeEvent;
use CL\Luna\Mapper\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class User extends Model {

    use SchemaTrait;
    use Nested;

    public static function scopeUnregistered($query)
    {
        return $query->where('user.address_id != ""');
    }

    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $password;

    /**
     * @var integer
     */
    public $addressId;


    public $isBlocked = false;

    public $deletedAt;
    public $test;

    /**
     * @return Address
     */
    public function getAddress()
    {
        return Repo::get()->loadLink($this, 'address')->get();
    }

    /**
     * @return Address
     */
    public function setAddress(Address $address)
    {
        return Repo::get()->loadLink($this, 'address')->set($address);
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return Repo::get()->loadLink($this, 'profile')->get();
    }

    /**
     * @return Profile
     */
    public function setProfile(Profile $profile)
    {
        return Repo::get()->loadLink($this, 'profile')->set($profile);
    }

    /**
     * @return Collection
     */
    public function getPosts()
    {
        return Repo::get()->loadLink($this, 'posts');
    }

    public static function test($model)
    {
        var_dump('User event "test" called');
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setSoftDelete(true)

            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
                new Field\Password('password'),
                new Field\Boolean('isBlocked'),
                new Field\Integer('addressId'),
                new Field\Timestamp('deletedAt'),
            ])

            ->setRels([
                new Rel\BelongsTo('address', $schema, Address::getSchema()),

                new Rel\HasMany('posts', $schema, Post::getSchema(), [
                    // 'cascade' => Rel\AbstractRel::UNLINK
                ]),

                new Rel\HasOne('profile', $schema, Profile::getSchema(), [
                    // 'cascade' => Rel\AbstractRel::DELETE
                ]),
            ])

            ->setAsserts([
                new Assert\Present('name'),
            ])

            ->getEventListeners()
                ->addBefore(NodeEvent::SAVE, 'CL\Luna\Test\User::test');
    }

}
