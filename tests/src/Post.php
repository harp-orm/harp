<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field\Integer;
use CL\Luna\Field\String;
use CL\Luna\Rel\BelongsTo;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Post extends Model {

    use SchemaTrait;

    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $userId;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $body;

    /**
     * @return LinkOne
     */
    public function getUser()
    {
        return parent::getLinkByName('user');
    }

    public function setUser(User $user)
    {
        return parent::setLinkByName('user', $user);
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setRels([
                new BelongsTo('user', User::getSchema()),
            ]);

        $schema
            ->setFields([
                new Integer('id'),
                new String('title'),
                new String('body'),
            ]);

        $schema
            ->setAsserts([
                new Assert\Present('title'),
            ]);
    }

}
