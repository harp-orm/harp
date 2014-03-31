<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\Model\LinkMany;
use CL\Luna\Model\AbstractLink;
use CL\Luna\Util\Arr;
use CL\Luna\Repo\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends AbstractRel
{
    protected $foreignKey;

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function initialize()
    {
        if ( ! $this->foreignKey)
        {
            $this->foreignKey = $this->getSchema()->getName().'Id';
        }
    }

    public function setLinks(array $models, array $related)
    {
        $related = Arr::indexGroup($related, $this->getForeignKey());

        foreach ($models as $model)
        {
            $index = $model->{$this->getKey()};
            $foreginModels = isset($related[$index]) ? $related[$index] : array();

            Repo::getInstance()->setLink($model, $this->getName(), new LinkMany($this, $foreginModels));

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

    public function update(Model $model, AbstractLink $link)
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
