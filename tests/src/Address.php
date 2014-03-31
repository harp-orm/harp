<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field\String;
use CL\Luna\Field\Integer;
use CL\Luna\Rel\HasMany;
use CL\Luna\Repo\Repo;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Address extends Model {

    use SchemaTrait;

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
     * @return Post
     */
    public function users()
    {
        return Repo::getInstance()->getLink($this, 'users');
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setRels([
                new HasMany('users', User::getSchema()),
            ])
            ->setAsserts([
                new Assert\Present('location'),
            ])
            ->setFields([
                new Integer('id'),
                new String('zipCode'),
                new String('location'),
            ]);
    }

}
