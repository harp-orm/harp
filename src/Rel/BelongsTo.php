<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Repo\LinkOne;
use CL\Luna\Field\Integer;
use CL\Luna\Schema\Schema;
use CL\Luna\Repo\Repo;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends AbstractRel implements LinkOneInterface
{
    protected $key;

    public function getKey()
    {
        return $this->key;
    }

    public function getForeignKey()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function loadForeignModels(array $models)
    {
        $keys = $this->getKeysFrom($models);

        return $keys ? $this->getForeignSchema()->getSelectQuery()->where([$this->getForeignKey() => $keys])->execute()->fetchAll() : array();
    }

    public function groupForeignModels(array $models, array $foreign, Closure $yield)
    {
        $foreign = Arr::index($foreign, $this->getForeignKey());

        foreach ($models as $model)
        {
            $index = $model->{$this->getKey()};

            $foreginModel = isset($foreign[$index]) ? $foreign[$index] : $this->getForeignSchema()->newNotLoadedModel();

            $yield($model, new LinkOne($this, $foreginModel));
        }

        return $this;
    }

    public function initialize()
    {
        if ( ! $this->key)
        {
            $this->key = $this->getForeignSchema()->getName().'Id';
        }

        $this->getSchema()->getFields()->add(new Integer($this->key));
    }

    public function update(Model $model, LinkOne $link)
    {
        if ($link->get()->isPersisted())
        {
            $model->{$this->getKey()} = $link->get()->getId();
        }
    }

    public function joinRel($query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query->join([$this->getForeignTable() => $this->getName()], $condition);
    }
}
