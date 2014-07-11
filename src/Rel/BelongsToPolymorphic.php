<?php

namespace Harp\Harp\Rel;

use Harp\Util\Arr;
use Harp\Harp\Config;
use Harp\Harp\Repo;
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\LinkOne;
use Harp\Query\AbstractWhere;
use BadMethodCallException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsToPolymorphic extends AbstractRelOne implements UpdateOneInterface
{
    protected $key;
    protected $classKey;

    public function __construct($name, Config $config, Repo $defaultRepo, array $options = array())
    {
        $this->key = $name.'Id';
        $this->classKey = $name.'Class';

        parent::__construct($name, $config, $defaultRepo, $options);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getClassKey()
    {
        return $this->classKey;
    }

    public function getForeignKey()
    {
        return $this->getConfig()->getPrimaryKey();
    }

    public function hasModels(Models $models)
    {
        return ! ($models->isEmptyProperty($this->key) or $models->isEmptyProperty($this->classKey));
    }

    public function loadModels(Models $models, $flags = null)
    {
        $models = $models->filter(function ($model) {
            return $model->{$this->classKey};
        });

        $groups = Arr::groupBy($models->toArray(), function($model){
            return $model->{$this->classKey};
        });

        foreach ($groups as $modelClass => & $models) {

            $keys = Arr::pluckUniqueProperty($models, $this->key);

            if ($keys) {
                $models = $modelClass::findAll()
                    ->whereIn($this->getForeignKey(), $keys)
                    ->loadRaw($flags);
            } else {
                $models = [];
            }
        }

        return Arr::flatten($groups);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return (
            $model->{$this->key} == $foreign->{$this->getForeignKey()}
            and $model->{$this->classKey} == get_class($foreign)
        );
    }

    public function update(LinkOne $link)
    {
        $link->getModel()->{$this->key} = $link->get()->getId();
        $link->getModel()->{$this->classKey} = get_class($link->get());
    }

    public function join(AbstractWhere $query, $parent)
    {
        throw new BadMethodCallException('BelongsToPolymorphic does not support join');
    }
}
