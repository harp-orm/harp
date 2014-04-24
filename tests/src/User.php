<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Model\Schema;
use CL\Luna\Model\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

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

    public $id;
    public $name;
    public $password;
    public $addressId;
    public $isBlocked = false;
    public $deletedAt;
    public $locationId;
    public $locationClass;
    public $test;

    public function getAddress()
    {
        return $this->loadRelLink('address')->get();
    }

    public function setAddress(Address $address)
    {
        return $this->loadRelLink('address')->set($address);
    }

    public function getLocation()
    {
        return $this->loadRelLink('location')->get();
    }

    public function setLocation(LocationInterface $location)
    {
        return $this->loadRelLink('location')->set($location);
    }

    public function getProfile()
    {
        return $this->loadRelLink('profile')->get();
    }

    public function setProfile(Profile $profile)
    {
        return $this->loadRelLink('profile')->set($profile);
    }

    public function getPosts()
    {
        return $this->loadRelLink('posts');
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
                new Field\Integer('locationId'),
                new Field\String('locationClass'),
                new Field\Timestamp('deletedAt'),
            ])

            ->setRels([
                new Rel\BelongsTo('address', $schema, Address::getSchema()),
                new Rel\BelongsToPolymorphic('location', $schema, City::getSchema()),

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

            ->setEventBeforeSave('CL\Luna\Test\User::test');
    }

}
