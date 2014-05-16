<?php

namespace CL\Luna\Rel;

use CL\Util\Arr;
use CL\Util\Objects;
use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Rel\InsertInterface;
use CL\LunaCore\Rel\DeleteInterface;
use CL\LunaCore\Rel\AbstractRelMany;
use CL\Luna\Query\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasManyThrough extends AbstractRelMany implements RelJoinInterface, InsertInterface, DeleteInterface
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
                Arr::pluckUniqueProperty($models, $this->getRepo()->getPrimaryKey())
            );

        return $select->loadRaw();
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
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

    public function delete(AbstractModel $model, AbstractLink $link)
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

    public function insert(AbstractModel $model, AbstractLink $link)
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
