<?php namespace CL\Luna\Rel;

use CL\Luna\Schema\Schema;
use CL\Luna\Mapper\RelInterface;
use CL\Luna\Util\Arr;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRel implements RelInterface
{
    const UNLINK = 1;
    const DELETE = 2;

    protected $foreignSchema;
    protected $schema;
    protected $name;
    protected $cascade;

    public function __construct($name, Schema $schema, Schema $foreignSchema, array $options = array())
    {
        $this->foreignSchema = $foreignSchema;
        $this->name = $name;
        $this->schema = $schema;

        foreach ($options as $name => $value) {
            $this->$name = $value;
        }
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

    public function getKeysFrom(array $models, $key)
    {
        return array_filter(array_unique(Arr::extract($models, $key)));
    }

    public function loadForeignNodes(array $models)
    {
        $keys = $this->getKeysFrom($models, $this->getKey());

        if ($keys) {
            return $this
                ->getForeignSchema()
                    ->select([
                        $this->getForeignKey() => $keys
                    ]);
        } else {
            return array();
        }
    }

    abstract public function getKey();
    abstract public function getForeignKey();
}
