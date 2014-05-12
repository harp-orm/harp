<?php

namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Objects;
use CL\Luna\Model\AbstractDbRepo;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasManyThrough extends Mapper\AbstractRelMany implements RelJoinInterface, Mapper\RelInsertInterface, Mapper\RelDeleteInterface
{
    protected $foreignKey;
    protected $through;

    public function __construct($name, AbstractDbRepo $repo, AbstractDbRepo $foreignRepo, $through, array $options = array())
    {
        $this->through = $through;
        $this->foreignKey = $foreignRepo->getName().'Id';
        $this->key = $repo->getName().'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getThroughRel()
    {
        return $this->getRepo()->getRel($this->through);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getThroughKey()
    {
        return $this->getName().'Key';
    }

    public function hasForeign(array $models)
    {
        return ! empty($models);
    }

    public function getThroughTable()
    {
        return $this->getThroughRel()->getName();
    }

    public function loadForeign(array $models)
    {
        $throughKey = $this->getThroughTable().'.'.$this->getThroughRel()->getForeignKey();
        $throughForeignKey = $this->getThroughTable().'.'.$this->key;
        $store = $this->getForeignRepo();

        $select = $store->findAll()
            ->column($throughKey, $this->getTHroughKey())
            ->joinRels($this->through)
            ->where(
                $throughForeignKey,
                Arr::extractUnique($models, $this->getRepo()->getPrimaryKey())
            );

        return $select->loadRaw();
    }

    public function areLinked(Mapper\AbstractNode $model, Mapper\AbstractNode $foreign)
    {
        return $model->getId() == $foreign->{$this->getThroughKey()};
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->foreignRepo->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignRepo());

        $query
            ->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }

    public function delete(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        $removed = new SplObjectStorage();

        foreach ($link->getRemoved() as $removed) {
            foreach ($model->{$this->through} as $item) {
                if ($item->{$this->foreignKey} == $removed->getId()) {
                    $item->delete();
                    $removed->attach($item);
                }
            }
        }

        return $removed;
    }

    public function insert(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        $inserted = new SplObjectStorage();

        if (count($link->getAdded()) > 0) {
            $throughRepo = $this->getThroughRel()->getForeignRepo();
            $through = $item->{$this->foreignKey};

            foreach ($link->getAdded() as $added) {
                $item = $throughRepo->newInstance([
                    $this->getThroughRel()->getForeignKey() => $model->getId(),
                    $this->foreignKey => $added->getId(),
                ]);

                $through->add($item);

                $inserted->attach($item);
            }
        }

        return $inserted;
    }
}
