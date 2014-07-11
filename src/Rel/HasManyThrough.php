<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Config;
use Harp\Harp\Repo;
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\LinkMany;
use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyThrough extends AbstractRelMany implements DeleteManyInterface, InsertManyInterface
{
    protected $key;
    protected $foreignKey;
    protected $through;

    public function __construct($name, Config $config, Repo $repo, $through, array $options = array())
    {
        $this->through = $through;

        parent::__construct($name, $config, $repo, $options);
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        if (! $this->foreignKey) {
            $this->foreignKey = lcfirst($this->getRepo()->getTable()).'Id';
        }

        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        if (! $this->key) {
            $this->key = lcfirst($this->getConfig()->getTable()).'Id';
        }

        return $this->key;
    }

    /**
     * @return AbstractRel|null
     */
    public function getThroughRel()
    {
        return $this->getConfig()->getRel($this->through);
    }

    /**
     * @return Repo
     */
    public function getThroughRepo()
    {
        return $this->getThroughRel()->getRepo();
    }

    /**
     * @return string
     */
    public function getThroughTable()
    {
        return $this->getThroughRel()->getName();
    }

    /**
     * @return string
     */
    public function getThroughKey()
    {
        return $this->getName().'Key';
    }

    /**
     * @param  Models  $models
     * @return boolean
     */
    public function hasModels(Models $models)
    {
        return ! $models->isEmptyProperty($this->getConfig()->getPrimaryKey());
    }

    /**
     * @param  Models $models
     * @param  int $flags
     * @return AbstractModel[]
     */
    public function loadModels(Models $models, $flags = null)
    {
        $throughKey = $this->getThroughTable().'.'.$this->getThroughRel()->getForeignKey();
        $throughForeignKey = $this->getThroughTable().'.'.$this->getKey();

        $keys = $models->getIds();

        return $this
            ->findAllWhereIn($throughForeignKey, $keys, $flags)
            ->column($throughKey, $this->getThroughKey())
            ->joinRels([$this->through])
            ->loadRaw();
    }

    /**
     * @param  AbstractModel $model
     * @param  AbstractModel $foreign
     * @return boolean
     */
    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->getId() == $foreign->{$this->getThroughKey()};
    }

    /**
     * @param  AbstractWhere $query
     * @param  strng         $parent
     */
    public function join(AbstractWhere $query, $parent)
    {
        $alias = $this->getName();
        $conditions = [
            "$alias.{$this->getRepo()->getPrimaryKey()}" => "{$this->through}.{$this->getForeignKey()}"
        ];

        $conditions += $this->getSoftDeleteConditions();

        $this->getThroughRel()->join($query, $parent);

        $query->joinAliased($this->getRepo()->getTable(), $alias, $conditions);
    }

    public function delete(LinkMany $link)
    {
        $through = $link->getModel()->getLink($this->through);
        $removedIds = $link->getRemoved()->getIds();

        $removedItems = $through->get()->filter(function ($item) use ($removedIds) {
            return in_array($item->{$this->getForeignKey()}, $removedIds);
        });

        $through->get()->removeAll($removedItems);

        foreach ($removedItems as $item) {
            $item->delete();
        }

        return $removedItems;
    }

    public function insert(LinkMany $link)
    {
        $inserted = new Models();

        if (count($link->getAdded()) > 0) {
            $through = $link->getModel()->getLink($this->through);
            $repo = $this->getThroughRepo();

            foreach ($link->getAdded() as $added) {
                $inserted->add($repo->newModel([
                    $this->getKey() => $link->getModel()->getId(),
                    $this->getForeignKey() => $added->getId(),
                ]));
            }

            $through->get()->addAll($inserted);
        }

        return $inserted;
    }
}
