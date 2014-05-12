<?php

namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Util\Objects;
use CL\Luna\Mapper;
use CL\Luna\Model\AbstractRepo;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsToPolymorphic extends Mapper\AbstractRelOne implements Mapper\RelUpdateInterface
{
    protected $key;
    protected $storeKey;

    public function __construct($name, AbstractRepo $store, AbstractRepo $defaultForeignRepo, array $options = array())
    {
        $this->key = $name.'Id';
        $this->storeKey = $name.'Class';

        parent::__construct($name, $store, $defaultForeignRepo, $options);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getRepoKey()
    {
        return $this->storeKey;
    }

    public function getForeignKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    public function hasForeign(array $models)
    {
        return true;
    }

    public function loadForeign(array $models)
    {
        $groups = Arr::groupBy($models, function($model){
            return $model->{$this->storeKey};
        });

        foreach ($groups as $modelClass => & $models) {

            $keys = Arr::extractUnique($models, $this->key);
            $store = (new $modelClass())->getRepo();

            if ($keys) {
                $models = $store->findAll()
                    ->where($this->getForeignKey(), $keys)
                    ->loadRaw();
            }
        }

        return Arr::flatten($groups);
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::combineArrays($models, $foreign, function($model, $foreign){
            return (
                $model->{$this->key} == $foreign->{$this->getForeignKey()}
                and $model->{$this->storeKey} == get_class($foreign)
            );
        });
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        $model->{$this->key} = $link->get()->getId();
        $model->{$this->storeKey} = $link->get()->getRepo()->getName();
    }

    public function loadForeignRepo(array $data)
    {
        if (isset($data['_class'])) {
            $class = $data['_class'];
            return $class::getRepo();
        }

        return $this->getForeignRepo();
    }

    public function loadFromData(array $data)
    {
        if (isset($data['_id'])) {
            $store = $this->loadForeignRepo($data);

            return $store->find($data['_id']);
        }
    }
}
