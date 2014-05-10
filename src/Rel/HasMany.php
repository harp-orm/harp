<?php

namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Objects;
use CL\Luna\Model\Repo;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends Mapper\AbstractRelMany implements RelJoinInterface
{
    use LoadFromDataTrait;

    protected $foreignKey;

    public function __construct($name, Repo $store, Repo $foreignRepo, array $options = array())
    {
        $this->foreignKey = lcfirst($store->getName()).'Id';

        parent::__construct($name, $store, $foreignRepo, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->getKey()));
    }

    public function loadForeign(array $models)
    {
        return $this->getForeignRepo()
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
        $columns = [$this->getForeignKey() => $this->foreignRepo->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->foreignRepo);

        $query->joinAliased($this->foreignRepo->getTable(), $this->getName(), $condition);
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }

        foreach ($link->getRemoved() as $added) {
            $added->{$this->getForeignKey()} = null;
        }
    }
}
