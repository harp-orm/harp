<?php

namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Objects;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;
use CL\Luna\Model\Store;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends Mapper\AbstractRelOne implements RelJoinInterface
{
    use LoadFromDataTrait;

    protected $foreignKey;

    public function __construct($name, Store $store, Store $foreignStore, array $options = array())
    {
        $this->foreignKey = $store->getName().'Id';

        parent::__construct($name, $store, $foreignStore, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getPrimaryKey();
    }

    public function getForeignStore()
    {
        return $this->foreignStore;
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->foreignKey));
    }

    public function loadForeign(array $models)
    {
        $store = $this->getForeignStore();

        return $store->findAll()
            ->where(
                $this->getKey(),
                Arr::extractUnique($models, $this->foreignKey)
            )
            ->loadRaw();
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::combineArrays($models, $foreign, function($model, $foreign){
            return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
        });
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        if ($link->isChanged())
        {
            $link->get()->{$this->getForeignKey()} = $model->{$this->getKey()};
            $link->getOriginal()->{$this->getForeignKey()} = NULL;
        }
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getForeignStore()->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignStore());

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }
}
