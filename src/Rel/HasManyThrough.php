<?php

namespace Harp\Db\Rel;

use Harp\Db\AbstractDbRepo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Rel\AbstractRelMany;
use Harp\Core\Rel\DeleteManyInterface;
use Harp\Core\Rel\InsertManyInterface;
use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasManyThrough extends AbstractRelMany implements DbRelInterface, DeleteManyInterface, InsertManyInterface
{
    protected $key;
    protected $foreignKey;
    protected $through;

    public function __construct($name, AbstractDbRepo $repo, AbstractDbRepo $foreignRepo, $through, array $options = array())
    {
        $this->through = $through;
        $this->foreignKey = lcfirst($foreignRepo->getName()).'Id';
        $this->key = lcfirst($repo->getName()).'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return DbRelInterface
     */
    public function getThroughRel()
    {
        return $this->getRepo()->getRel($this->through);
    }

    /**
     * @return AbstractDbRepo
     */
    public function getThroughRepo()
    {
        return $this->getThroughRel()->getForeignRepo();
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
    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->getRepo()->getPrimaryKey());
    }

    /**
     * @param  Models $models
     * @param  int $flags
     * @return AbstractModel[]
     */
    public function loadForeign(Models $models, $flags = null)
    {
        $throughKey = $this->getThroughTable().'.'.$this->getThroughRel()->getForeignKey();
        $throughForeignKey = $this->getThroughTable().'.'.$this->key;
        $repo = $this->getForeignRepo();

        $keys = $models->getIds();

        $select = $repo->findAll()
            ->column($throughKey, $this->getTHroughKey())
            ->joinRels([$this->through])
            ->whereIn($throughForeignKey, $keys);

        return $select->loadRaw($flags);
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
        $condition = "ON $alias.{$this->getForeignRepo()->getPrimaryKey()} = {$this->through}.{$this->getForeignKey()}";

        if ($this->getForeignRepo()->getSoftDelete()) {
            $condition .= " AND $alias.deletedAt IS NULL";
        }

        $this->getThroughRel()->join($query, $parent);

        $query
            ->joinAliased($this->getForeignRepo()->getTable(), $alias, $condition);
    }

    public function delete(AbstractModel $model, LinkMany $link)
    {
        $through = $this->getRepo()->loadLink($model, $this->through);
        $removedIds = $link->getRemoved()->getIds();

        $removedItems = $through->get()->filter(function ($item) use ($removedIds) {
            return in_array($item->{$this->foreignKey}, $removedIds);
        });

        $through->get()->removeAll($removedItems);

        foreach ($removedItems as $item) {
            $item->delete();
        }

        return $removedItems;
    }

    public function insert(AbstractModel $model, LinkMany $link)
    {
        $inserted = new Models();

        if (count($link->getAdded()) > 0) {
            $through = $this->getRepo()->loadLink($model, $this->through);
            $repo = $this->getThroughRepo();

            foreach ($link->getAdded() as $added) {
                $inserted->add($repo->newModel([
                    $this->key => $model->getId(),
                    $this->foreignKey => $added->getId(),
                ]));
            }

            $through->get()->addAll($inserted);
        }

        return $inserted;
    }
}
