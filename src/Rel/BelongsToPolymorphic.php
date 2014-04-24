<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Util\Storage;
use CL\Luna\Model\Model;
use CL\Luna\Mapper;
use CL\Luna\Schema\Schema;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsToPolymorphic extends Mapper\AbstractRelOne
{
    protected $key;
    protected $schemaKey;

    public function __construct($name, Schema $schema, Schema $defaultForeignSchema, array $options = array())
    {
        $this->key = $name.'Id';
        $this->schemaKey = $name.'Class';

        parent::__construct($name, $schema, $defaultForeignSchema, $options);
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

    public function hasForeign(array $models)
    {
        return true;
    }

    public function loadForeign(array $models)
    {
        $groups = Arr::groupBy($models, function($model){
            return $model->{$this->schemaKey};
        });

        foreach ($groups as $modelClass => & $models) {

            $keys = Arr::extractUnique($models, $this->key);
            $schema = $modelClass::getSchema();

            if ($keys) {
                $models = $schema
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
                and $model->{$this->schemaKey} == get_class($foreign)
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

    public function loadFromData(array $data)
    {
        if (isset($data['_id'])) {
            $foreignSchema = $this->getForeignSchema();

            if (isset($data['_class'])) {
                $class = $data['_class'];
                $foreignSchema = $class::getSchema();
            }

            return $foreignSchema
                ->getSelectQuery()
                ->whereKey($data['_id'])
                ->first();
        }
    }
}
