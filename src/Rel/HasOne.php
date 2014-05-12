<?php

namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;
use CL\Luna\Model\AbstractDbRepo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends Mapper\AbstractRelOne implements RelJoinInterface, Mapper\RelUpdateInterface
{
    protected $foreignKey;

    public function __construct($name, AbstractDbRepo $store, AbstractDbRepo $foreignRepo, array $options = array())
    {
        $this->foreignKey = $name.'Id';

        parent::__construct($name, $store, $foreignRepo, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getPrimaryKey();
    }

    public function getForeignRepo()
    {
        return $this->foreignRepo;
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->foreignKey));
    }

    public function loadForeign(array $models)
    {
        $store = $this->getForeignRepo();

        return $store->findAll()
            ->where(
                $this->getKey(),
                Arr::extractUnique($models, $this->foreignKey)
            )
            ->loadRaw();
    }

    public function areLinked(Mapper\AbstractNode $model, Mapper\AbstractNode $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        if ($link->isChanged())
        {
            $link->get()->{$this->getForeignKey()} = $model->{$this->getKey()};
            $link->getOriginal()->{$this->getForeignKey()} = NULL;
        }
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getForeignRepo()->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignRepo());

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }
}
