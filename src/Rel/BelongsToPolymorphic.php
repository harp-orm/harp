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

        parent::__construct($name, $schema, reset($foreignSchemas), $options);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getSchemaKey()
    {
        return $this->schemaKey;
    }

    public function getForeignKey()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function getForeignSchemaName(Schema $schema)
    {
        return array_search($schema, $this->foreignSchemas);
    }

    public function getForeignSchemaForName($name)
    {
        return $this->foreignSchemas[$schemaName];
    }

    public function hasForeign(array $models)
    {
        return true;
    }

    public function loadForeign(array $models)
    {
        $groups = Arr::groupBy($models, function($model){
            return $model->{$this->schemaKey};
        });

        foreach ($groups as $schemaName => & $models) {

            $keys = Arr::extractUnique($models, $this->key);

            if ($keys) {
                $models = $this->getForeignSchemaForName($schemaName)
                    ->select([
                        $this->getForeignKey() => $keys
                    ]);
            }
        }

        return Arr::flatten($groups);
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Storage::combineArrays($models, $foreign, function($model, $foreign){
            return (
                $model->{$this->key} == $foreign->{$this->getForeignKey()}
                and $model->{$this->schemaKey} == $this->getForeignSchemaName($foreign)
            );
        });
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        if ($link->get()->isPersisted())
        {
            $model->{$this->key} = $link->get()->getId();
            $model->{$this->schemaKey} = $link->get()->getSchema()->getName();
        }
    }
}
