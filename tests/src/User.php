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

    /**
     * @return Address
     */
    public function getAddress()
    {
        return Repo::getLink($this, 'address')->get();
    }

    /**
     * @return Address
     */
    public function setAddress(Address $address)
    {
        return Repo::getLink($this, 'address')->set($address);
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return Repo::getLink($this, 'profile')->get();
    }

    /**
     * @return Profile
     */
    public function setProfile(Profile $profile)
    {
        return Repo::getLink($this, 'profile')->set($profile);
    }

    /**
     * @return Collection
     */
    public function getPosts()
    {
        return Repo::getLink($this, 'posts');
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
            ])

            ->setRels([
                new Rel\BelongsTo('address', Address::getSchema()),
                new Rel\HasMany('posts', Post::getSchema()),
                new Rel\HasOne('profile', Profile::getSchema()),
            ])

            ->setAsserts([
                new Assert\Present('name'),
            ])

            ->getEventListeners()
                ->add(ModelEvent::PERSIST, 'CL\Luna\Test\User::test');
    }

}
