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
class HasManyExclusive extends Mapper\AbstractRelMany implements RelJoinInterface, Mapper\DeleteCascadeInterface
{
    use LoadFromDataTrait;

    protected $foreignKey;

    public function __construct($name, Schema $schema, Schema $foreignSchema, array $options = array())
    {
        $this->foreignKey = lcfirst($schema->getName()).'Id';

        parent::__construct($name, $schema, $foreignSchema, $options);
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
        return $this->getForeignSchema()
            ->findAll()
            ->where(
                $this->foreignKey,
                Arr::extractUnique($models, $this->getKey())
            )
            ->loadRaw();
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

    public function delete(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        Objects::invoke($link->getRemoved(), 'delete');
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }
    }
}
