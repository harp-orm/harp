<?php namespace CL\Luna\Rel;

use CL\Luna\Model\Model;
use CL\Luna\Model\LinkMany;
use CL\Luna\Model\LinkInterface;
use CL\Luna\Util\Arr;

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
            $this->foreignKey = $this->getSchema()->getName().'_id';
        }
    }

    public function setLinks(array $models, array $related)
    {
        $related = Arr::indexGroup($related, $this->getForeignKey());

        foreach ($models as $model)
        {
            $index = $model->{$this->getKey()};
            $model->setLink($this, new LinkMany(isset($related[$index]) ? $related[$index] : array()));
        }
    }

    public function getSelect()
    {
        return $this->getForeignSchema()->getSelectSchema();
    }

    public function joinRel($query, $parent)
    {
        $table = $parent ?: $this->getTable();
        $columns = [$this->getForeignKey() => $this->getForeignPrimaryKey()];

        $query->join([$this->getForeignTable() => $this->getName()], $this->getJoinCondition($table, $columns));
    }

    public function update(Model $model, LinkInterface $related)
    {
        foreach ($related->getAdded() as $item)
        {
            $item->{$this->getForeignKey()} = $model->{$this->getKey()};
        }

        foreach ($related->getRemoved() as $item)
        {
            $item->{$this->getForeignKey()} = NULL;
        }
    }
}
