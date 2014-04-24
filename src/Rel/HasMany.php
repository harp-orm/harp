<?php

namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Objects;
use CL\Luna\Model\Schema;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends Mapper\AbstractRelMany implements RelJoinInterface
{
    use LoadFromDataTrait;

    protected $foreignKey;
    protected $deleteOnRemove;

    public function __construct($name, Schema $schema, Schema $foreignSchema, array $options = array())
    {
        $this->foreignKey = lcfirst($schema->getName()).'Id';

        parent::__construct($name, $schema, $foreignSchema, $options);
    }

    public function getDeleteOnRemove()
    {
        return $this->deleteOnRemove;
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getSchema()->getPrimaryKey();
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->getKey()));
    }

    public function loadForeign(array $models)
    {
        return $this
            ->foreignSchema
            ->select([
                $this->foreignKey => Arr::extractUnique($models, $this->getKey())
            ]);
    }

    public function linkToForeign(array $models, array $foreign)
    {
        $return = Objects::groupCombineArrays($models, $foreign, function ($model, $foreign) {
            return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
        });

        return $return;
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->foreignSchema->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->foreignSchema);

        $query->joinAliased($this->foreignSchema->getTable(), $this->getName(), $condition);
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }

        if ($this->deleteOnRemove) {
            Objects::invoke($link->getRemoved(), 'delete');
        } else {
            foreach ($link->getRemoved() as $added) {
                $added->{$this->getForeignKey()} = null;
            }
        }
    }
}
