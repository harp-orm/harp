<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Repo\LinkOne;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends AbstractRel implements LinkOneInterface
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
        $foreign = Arr::index($foreign, $this->getForeignKey());

        foreach ($models as $model)
        {
            $index = $model->{$this->getKey()};

            $foreginModel = isset($foreign[$index]) ? $foreign[$index] : $this->getForeignSchema()->newNotLoadedModel();

            $set_link($model, new LinkOne($this, $foreginModel));
        }
    }

    public function update(Model $model, LinkOne $link)
    {
        if ($link->isChanged())
        {
            $link->get()->{$this->getForeignKey()} = $model->{$this->getKey()};
            $link->getOriginal()->{$this->getForeignKey()} = NULL;
        }
    }

    public function joinRel($query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getForeignPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query->join([$this->getForeignTable() => $this->getName()], $condition);
    }
}
