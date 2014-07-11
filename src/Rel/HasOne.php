<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\LinkOne;
use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasOne extends AbstractRelOne implements UpdateOneInterface
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
     * @param  int    $flags
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

    public function update(LinkOne $link)
    {
        if ($link->isChanged())
        {
            $link->get()->{$this->getForeignKey()} = $link->getModel()->{$this->getKey()};
            $link->getOriginal()->{$this->getForeignKey()} = null;
        }
    }

    /**
     * @param  AbstractWhere $query
     * @param  string        $parent
     */
    public function join(AbstractWhere $query, $parent)
    {
        $conditions["{$this->getName()}.{$this->getForeignKey()}"] = "$parent.{$this->getKey()}";

        $conditions += $this->getSoftDeleteConditions();

        $query->joinAliased($this->getRepo()->getTable(), $this->getName(), $conditions);
    }
}
