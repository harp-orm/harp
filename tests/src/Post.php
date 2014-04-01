<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Repo\Repo;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel\BelongsTo;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Post extends Model {

    use SchemaTrait;

    public $id;
    public $title;
    public $body;
    public $price;
    public $tags;
    public $createdAt;
    public $updatedAt;
    public $publishedAt;
    public $userId;

    /**
     * @return LinkOne
     */
    public function getUser()
    {
        return Repo::getLink($this, 'user')->get();
    }

    public function setUser(User $user)
    {
        return Repo::getLink($this, 'user')->set($user);
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setRels([
                new BelongsTo('user', User::getSchema()),
            ]);

        $schema
            ->setFields([
                new Field\Integer('id'),
                new Field\String('title'),
                new Field\Text('body'),
                new Field\Decimal('price'),
                new Field\Serialized('tags', Field\Serialized::CSV),
                new Field\Timestamp('createdAt'),
                new Field\Timestamp('updatedAt'),
                new Field\DateTime('publishedAt'),
            ]);

        $schema
            ->setAsserts([
                new Assert\Present('title'),
            ]);
    }

}
