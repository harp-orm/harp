<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel\BelongsTo;
use CL\Luna\Rel\HasMany;
use CL\Carpo\Assert;
use CL\Luna\Model\ModelEvent;

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

    /**
     * @return Post
     */
    public function getAddress()
    {
        return parent::getLinkByName('address');
    }

    /**
     * @return Collection
     */
    public function getPosts()
    {
        return parent::getLinkByName('posts');
    }

    public static function test($model)
    {
        var_dump('User event "test" called');
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setSoftDelete(TRUE)

            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
                new Field\Password('password'),
                new Field\Boolean('isBlocked'),
            ])

            ->setRels([
                new BelongsTo('address', Address::getSchema()),
                new HasMany('posts', Post::getSchema()),
            ])

            ->setAsserts([
                new Assert\Present('name'),
            ])

            ->getEventListeners()
                ->add(ModelEvent::PERSIST, 'CL\Luna\Test\User::test');
    }

}
