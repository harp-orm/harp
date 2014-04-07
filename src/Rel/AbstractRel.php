<?php namespace CL\Luna\Rel;

use CL\Luna\Schema\Schema;
use CL\Luna\Model\Model;
use CL\Luna\Util\Arr;
use Closure;
use InvalidArgumentException;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRel
{
    protected static $allowedCascade = array(
        self::UNLINK,
        self::DELETE,
    );

    const UNLINK = 1;
    const DELETE = 2;

    protected $foreignSchema;
    protected $schema;
    protected $name;
    protected $cascade;

    public function __construct($name, Schema $foreign_schema)
    {
        $this->foreignSchema = $foreign_schema;
        $this->name = $name;
    }

    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getCascade()
    {
        return $this->cascade;
    }

    public function setCascade($cascade)
    {
        if (! in_array($cascade, self::$allowedCascade)) {
            throw new InvalidArgumentException('Not a valid cascade option');
        }

        $this->cascade = $cascade;

        return $this;
    }

    public function getForeignSchema()
    {
        return $this->foreignSchema;
    }

    public function getForeignTable()
    {
        return $this->getForeignSchema()->getTable();
    }

    public function getTable()
    {
        return $this->getSchema()->getTable();
    }

    public function getPrimaryKey()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function getForeignPrimaryKey()
    {
        return $this->getForeignSchema()->getPrimaryKey();
    }

    public function getKeysFrom(array $models)
    {
        return array_filter(array_unique(Arr::extract($models, $this->getKey())));
    }

    public function loadForeignModels(array $models)
    {
        $keys = $this->getKeysFrom($models);

        if (empty($keys)) {
            return array();
        } else {
            $query = $this
                ->getForeignSchema()
                    ->getSelectQuery();

            $models = $query
                ->where([$this->getForeignKey() => $keys])
                ->execute()
                ->fetchAll();

            return $models;
        }
    }

    abstract public function initialize();
    abstract public function getKey();
    abstract public function getForeignKey();
    abstract public function groupForeignModels(array $models, array $related, Closure $set_link);
}
