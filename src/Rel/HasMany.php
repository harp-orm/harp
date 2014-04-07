<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\Repo\LinkMany;
use CL\Luna\Util\Arr;
use CL\Luna\Repo\Repo;
use SplObjectStorage;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends AbstractMany
{
    protected $foreignKey;
    protected $deleteOnRemove;

    public function getDeleteOnRemove()
    {
        return $this->deleteOnRemove;
    }

    public function setDeleteOnRemove($delete_on_remove)
    {
        $this->deleteOnRemove = (bool) $delete_on_remove;

        return $this;
    }

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

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }

    public function deleteModels(SplObjectStorage $models)
    {
        foreach ($models as $model) {
            $model->delete();
        }
    }

    public function setModels(SplObjectStorage $models, $key)
    {
        foreach ($models as $model) {
            $model->{$this->getForeignKey()} = $key;
        }
    }

    public function cascadeDelete(Model $model, LinkMany $link)
    {
        if ($this->getCascade() === AbstractRel::NULLIFY) {
            $this->setModels($link->all(), null);
        } elseif ($this->getCascade() === AbstractRel::DELETE) {
            $this->deleteModels($link->all());
        }
    }

    public function update(Model $model, LinkMany $link)
    {
        $this->setModels($link->getAdded(), $model->{$this->getKey()});

        if ($this->getDeleteOnRemove()) {
            $this->deleteModels($link->getRemoved());
        } else {
            $this->setModels($link->getRemoved(), null);
        }
    }
}
