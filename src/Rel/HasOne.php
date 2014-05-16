<?php

namespace CL\Luna\Rel;

use CL\Util\Arr;
use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\AbstractLink;
use CL\LunaCore\Rel\UpdateInterface;
use CL\LunaCore\Rel\AbstractRelOne;
use CL\Luna\Query\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends AbstractRelOne implements RelJoinInterface, UpdateInterface
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
        $keys = Arr::pluckUniqueProperty($models, $this->foreignKey);
        return ! empty($keys);
    }

    public function loadForeign(array $models)
    {
        $store = $this->getForeignRepo();

        return $store->findAll()
            ->where(
                $this->getKey(),
                Arr::pluckUniqueProperty($models, $this->foreignKey)
            )
            ->loadRaw();
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    public function update(AbstractModel $model, AbstractLink $link)
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
