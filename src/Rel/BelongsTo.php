<?php

namespace CL\Luna\Rel;

use CL\Luna\Util\Arr;
use CL\Luna\Util\Objects;
use CL\Luna\Model\Store;
use CL\Luna\Mapper;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class BelongsTo extends Mapper\AbstractRelOne implements RelJoinInterface
{
    use LoadFromDataTrait;

    protected $key;

    public function __construct($name, Store $Store, Store $foreignStore, array $options = array())
    {
        $this->key = $name.'Id';

        parent::__construct($name, $Store, $foreignStore, $options);
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->key));
    }

    public function loadForeign(array $models)
    {
        return $this->getForeignStore()
            ->findAll()
            ->where(
                $this->getForeignKey(),
                Arr::extractUnique($models, $this->key)
            )
            ->loadRaw();
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::combineArrays($models, $foreign, function($model, $foreign){
            return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
        });
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getForeignKey()
    {
        return $this->getStore()->getPrimaryKey();
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        if ($link->get()->isPersisted())
        {
            $model->{$this->getKey()} = $link->get()->getId();
        }
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignStore());

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }

}
