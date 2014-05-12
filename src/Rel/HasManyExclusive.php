<?php

namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\Model\AbstractDbRepo;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasManyExclusive extends Mapper\AbstractRelMany implements RelJoinInterface, Mapper\RelDeleteInterface
{
    protected $foreignKey;

    public function __construct($name, AbstractDbRepo $repo, AbstractDbRepo $foreignRepo, array $options = array())
    {
        $this->foreignKey = lcfirst($repo->getName()).'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->getKey()));
    }

    public function loadForeign(array $models)
    {
        return $this->getForeignRepo()
            ->findAll()
            ->where(
                $this->foreignKey,
                Arr::extractUnique($models, $this->getKey())
            )
            ->loadRaw();
    }

    public function areLinked(Mapper\AbstractNode $model, Mapper\AbstractNode $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->foreignRepo->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->foreignRepo);

        $query->joinAliased($this->foreignRepo->getTable(), $this->getName(), $condition);
    }

    public function delete(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        Objects::invoke($link->getRemoved(), 'delete');

        return $link->getRemoved();
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }
    }
}
