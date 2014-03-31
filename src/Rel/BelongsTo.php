<?php namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Model\Model;
use CL\Luna\Model\AbstractLink;
use CL\Luna\Model\LinkOne;
use CL\Luna\Field\Integer;
use CL\Luna\Schema\Schema;
use CL\Luna\Repo\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends AbstractRel
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

    public function getSelect()
    {
        return $this->getForeignSchema()->getSelectQuery();
    }

    public function setLinks(array $models, array $related)
    {
        $related = Arr::index($related, $this->getForeignKey());

        foreach ($models as $model)
        {
            $index = $model->{$this->getKey()};

            $foreginModel = isset($related[$index]) ? $related[$index] : $this->getForeignSchema()->newNotLoadedModel();

            Repo::getInstance()->setLink($model, $this->getName(), new LinkOne($this, $foreginModel));
        }
    }

    public function initialize()
    {
        if ( ! $this->key)
        {
            $this->key = $this->getForeignSchema()->getName().'Id';
        }

        $this->getSchema()->getFields()->add(new Integer($this->key));
    }

    public function update(Model $model, AbstractLink $link)
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
