<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\LinkMany;
use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasMany extends AbstractRelMany implements UpdateManyInterface
{
    protected $foreignKey;

    /**
     * @return string
     */
    public function getForeignKey()
    {
        if (! $this->foreignKey) {
            $this->foreignKey = lcfirst($this->getConfig()->getTable()).'Id';
        }

        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->getConfig()->getPrimaryKey();
    }

    /**
     * @param  Models  $models
     * @return boolean
     */
    public function hasModels(Models $models)
    {
        return ! $models->isEmptyProperty($this->getKey());
    }

    /**
     * @param  Models $models
     * @param  int $flags
     * @return AbstractModel[]
     */
    public function loadModels(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->getKey());

        return $this->findAllWhereIn($this->getForeignKey(), $keys, $flags)->loadRaw();
    }

    /**
     * @param  AbstractModel $model
     * @param  AbstractModel $foreign
     * @return boolean
     */
    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    /**
     * @param  AbstractWhere $query
     * @param  string        $parent
     */
    public function join(AbstractWhere $query, $parent)
    {
        $conditions = [
            "{$this->getName()}.{$this->getForeignKey()}" => "$parent.{$this->getKey()}"
        ];

        $conditions += $this->getSoftDeleteConditions();

        $query->joinAliased($this->getRepo()->getTable(), $this->getName(), $conditions);
    }

    /**
     * @param  LinkMany      $link
     */
    public function update(LinkMany $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $link->getModel()->{$this->getKey()};
        }

        foreach ($link->getRemoved() as $added) {
            $added->{$this->getForeignKey()} = null;
        }
    }
}
