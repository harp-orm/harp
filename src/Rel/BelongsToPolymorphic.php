<?php

namespace Harp\Harp\Rel;

use CL\Util\Arr;
use Harp\Harp\AbstractDbRepo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Rel\AbstractRelOne;
use Harp\Core\Rel\UpdateOneInterface;
use Harp\Query\AbstractWhere;
use BadMethodCallException;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsToPolymorphic extends AbstractRelOne implements DbRelInterface, UpdateOneInterface
{
    protected $key;
    protected $classKey;

    public function __construct($name, AbstractDbRepo $store, AbstractDbRepo $defaultForeignRepo, array $options = array())
    {
        $this->key = $name.'Id';
        $this->classKey = $name.'Class';

        parent::__construct($name, $store, $defaultForeignRepo, $options);
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
        return $this->getRepo()->getPrimaryKey();
    }

    public function hasForeign(Models $models)
    {
        return ! ($models->isEmptyProperty($this->key) or $models->isEmptyProperty($this->classKey));
    }

    public function loadForeign(Models $models, $flags = null)
    {
        $models = $models->filter(function ($model) {
            return $model->{$this->classKey};
        });

        $groups = Arr::groupBy($models->toArray(), function($model){
            return $model->{$this->classKey};
        });

        foreach ($groups as $modelClass => & $models) {

            $keys = Arr::pluckUniqueProperty($models, $this->key);
            $repo = (new $modelClass())->getRepo();

            if ($keys) {
                $models = $repo->findAll()
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

    public function update(AbstractModel $model, LinkOne $link)
    {
        $model->{$this->key} = $link->get()->getId();
        $model->{$this->classKey} = get_class($link->get());
    }

    public function join(AbstractWhere $query, $parent)
    {
        throw new BadMethodCallException('BelongsToPolymorphic does not support join');
    }
}
