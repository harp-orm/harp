<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\Repo\LinkMany;
use CL\Luna\Util\Arr;
use CL\Luna\Repo\Repo;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends AbstractRel implements LinkManyInterface
{
    protected $foreignKey;

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getPrimaryKey();
    }

    public function initialize()
    {
        if ( ! $this->foreignKey)
        {
            $this->foreignKey = $this->getSchema()->getName().'Id';
        }
    }

    public function loadForeignModels(array $models)
    {
        $keys = $this->getKeysFrom($models);

        return $keys ? $this->getForeignSchema()->getSelectQuery()->where([$this->getForeignKey() => $keys])->execute()->fetchAll() : array();
    }

    public function groupForeignModels(array $models, array $foreign, Closure $set_link)
    {
        $foreign = Arr::indexGroup($foreign, $this->getForeignKey());

        foreach ($models as $model)
        {
            $index = $model->{$this->getKey()};
            $foreginModels = isset($foreign[$index]) ? $foreign[$index] : array();

            $set_link($model, new LinkMany($this, $foreginModels));
        }
    }

    public function getSelect()
    {
        return $this->getForeignSchema()->getSelectQuery();
    }

    public function joinRel($query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getForeignPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query->join([$this->getForeignTable() => $this->getName()], $condition);
    }

    public function update(Model $model, LinkMany $link)
    {
        foreach ($link->getAdded() as $item)
        {
            $item->{$this->getForeignKey()} = $model->{$this->getKey()};
        }

        foreach ($link->getRemoved() as $item)
        {
            $item->{$this->getForeignKey()} = NULL;
        }
    }
}
