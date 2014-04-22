<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;
use CL\Luna\Mapper\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Tag extends Model {

    use SchemaTrait;

    public $id;
    public $name;

    /**
     * @return User
     */
    public function getPostTags()
    {
        return Repo::get()->loadLink($this, 'postTags');
    }

    /**
     * @return User
     */
    public function getPosts()
    {
        return Repo::get()->loadLink($this, 'posts');
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setFields([
                new Field\Integer('id'),
                new Field\String('name'),
            ])
            ->setRels([
                new Rel\HasMany('postTags', $schema, PostTag::getSchema()),
                new Rel\HasManyThrough('posts', $schema, Post::getSchema(), 'postTags'),
            ])
            ->setAsserts([
                new Assert\Present('name'),
            ]);
    }

}
