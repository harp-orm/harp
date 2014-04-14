<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Mapper;
use CL\Luna\Schema\Schema;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsToPlymorphic extends AbstractOne
{
    protected $key;
    protected $schemaKey;
    protected $foreignSchemas;

    public function __construct($name, Schema $schema, array $foreignSchemas, array $options = array())
    {
        $this->key = $name.'Id';
        $this->schemaKey = $name.'Schema';
        $this->foreignSchemas = $foreignSchemas;

        parent::__construct($name, $schema, $foreignSchema, $options);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getSchemaKey()
    {
        return $this->schemaKey;
    }

    public function getForeignSchemaName(Schema $schema)
    {
        return array_search($schema, $this->foreignSchemas);
    }

    public function getForeignKey()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function loadForeignNodes(array $models)
    {
        $groups = Arr::groupBy($models, function($model){
            return $model->{$this->schemaKey};
        });

        foreach ($groups as $group => & $models) {
            $schema = $this->foreignSchemas[$group];
            $conditions = [
                $this->getForeignKey() => $this->getKeysFrom($models)
            ];

            $models = $this->loadSchemaNodes($schema, $conditions);
        }

        return Arr::flatten($groups);
    }

    public function linkForeignKey(Mapper\AbstractNode $foreign)
    {
        return $this->getForeignSchemaName($foreign->getSchema()).'|'.$foreign->{$this->getForeignKey()};
    }

    public function linkKey(Mapper\AbstractNode $model)
    {
        return $model->{$this->getForeignKey()}.'|'.$model->{$this->getKey()};
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        if ($link->get()->isPersisted())
        {
            $model->{$this->key} = $link->get()->getId();
            $model->{$this->schemaKey} = $link->get()->getSchema()->getName();
        }
    }

    public function joinRel($query, $parent)
    {

    }
}
