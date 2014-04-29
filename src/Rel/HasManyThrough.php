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
class HasManyThrough extends Mapper\AbstractRelMany implements RelJoinInterface
{
    use LoadFromDataTrait;

    protected $foreignKey;
    protected $through;

    public function __construct($name, Schema $schema, Schema $foreignSchema, $through, array $options = array())
    {
        $this->through = $through;
        $this->foreignKey = $foreignSchema->getName().'Id';
        $this->key = $schema->getName().'Id';

        parent::__construct($name, $schema, $foreignSchema, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getThroughRel()
    {
        return $this->getSchema()->getRel($this->through);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getThroughKey()
    {
        return $this->getName().'Key';
    }

    public function hasForeign(array $models)
    {
        return ! empty($models);
    }

    public function getThroughTable()
    {
        return $this->getThroughRel()->getName();
    }

    public function loadForeign(array $models)
    {
        $throughKey = $this->getThroughTable().'.'.$this->getThroughRel()->getForeignKey();
        $throughForeignKey = $this->getThroughTable().'.'.$this->key;
        $schema = $this->getForeignSchema();

        $select = $schema->findAll()
            ->column($throughKey, $this->getTHroughKey())
            ->joinRels($this->through)
            ->where([
                $throughForeignKey => Arr::extractUnique($models, $this->getSchema()->getPrimaryKey())
            ]);

        return $select->loadRaw();
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Objects::groupCombineArrays($models, $foreign, function ($model, $foreign) {
            return $model->getId() == $foreign->{$this->getThroughKey()};
        });
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->foreignSchema->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query
            ->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        $throughReflection = $this->getThroughRel()->getForeignSchema()->getModelReflection();
        $through = $model->{$this->through};

        foreach ($link->getAdded() as $added) {
            $item = $throughReflection->newInstance([
                $this->getThroughRel()->getForeignKey() => $model->getId(),
                $this->foreignKey => $added->getId(),
            ]);

            $through->add($item);
        }

        foreach ($link->getRemoved() as $removed) {
            foreach ($through as $item) {
                if ($item->{$this->foreignKey} == $removed->getId()) {
                    $through->remove($item);
                }
            }
        }
    }
}
