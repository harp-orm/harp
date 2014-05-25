<?php

namespace CL\Luna\Rel;

use CL\Util\Arr;
use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Rel\UpdateOneInterface;
use CL\Atlas\Query\AbstractQuery;
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
        return true;
    }

    public function loadForeign(Models $models, $flags = null)
    {
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
            }
        }

        return Arr::flatten($groups);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return (
            $model->{$this->key} == $foreign->{$this->getForeignKey()}
            and $model->{$this->classKey} == get_class($foreign->getRepo())
        );
    }

    public function update(AbstractModel $model, LinkOne $link)
    {
        $model->{$this->key} = $link->get()->getId();
        $model->{$this->classKey} = get_class($link->get()->getRepo());
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        throw new BadMethodCallException('BelongsToPolymorphic does not support join');
    }
}
