<?php

namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Objects;
use CL\Luna\Model\Store;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasManyExclusive extends Mapper\AbstractRelMany implements RelJoinInterface, Mapper\DeleteCascadeInterface
{
    use LoadFromDataTrait;

    protected $foreignKey;

    public function __construct($name, Store $Store, Store $foreignStore, array $options = array())
    {
        $this->foreignKey = lcfirst($Store->getName()).'Id';

        parent::__construct($name, $Store, $foreignStore, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getStore()->getPrimaryKey();
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->getKey()));
    }

    public function loadForeign(array $models)
    {
        return $this->getForeignStore()
            ->findAll()
            ->where(
                $this->foreignKey,
                Arr::extractUnique($models, $this->getKey())
            )
            ->loadRaw();
    }

    public function linkToForeign(array $models, array $foreign)
    {
        $return = Objects::groupCombineArrays($models, $foreign, function ($model, $foreign) {
            return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
        });

        return $return;
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->foreignStore->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->foreignStore);

        $query->joinAliased($this->foreignStore->getTable(), $this->getName(), $condition);
    }

    public function delete(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        Objects::invoke($link->getRemoved(), 'delete');
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }
    }
}
