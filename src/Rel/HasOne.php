<?php

namespace CL\Luna\Rel;

use CL\Util\Arr;
use CL\Luna\AbstractDbRepo;
use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\Models;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Rel\AbstractRelOne;
use CL\LunaCore\Rel\UpdateOneInterface;
use CL\Atlas\Query\AbstractQuery;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends AbstractRelOne implements DbRelInterface, UpdateOneInterface
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

    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->foreignKey);
    }

    public function loadForeign(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->foreignKey);

        return $this->getForeignRepo()
            ->findAll()
            ->where($this->getForeignKey(), $keys)
            ->loadRaw($flags);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    public function update(AbstractModel $model, LinkOne $link)
    {
        if ($link->isChanged())
        {
            $link->get()->{$this->getForeignKey()} = $model->{$this->getKey()};
            $link->getOriginal()->{$this->getForeignKey()} = null;
        }
    }

    public function join(AbstractQuery $query, $parent)
    {
        $alias = $this->getName();
        $condition = "ON $alias.{$this->getForeignKey()} = $parent.{$this->getKey()}";

        if ($this->getForeignRepo()->getSoftDelete()) {
            $condition .= " AND $alias.deletedAt IS NULL";
        }

        $query->joinAliased($this->getForeignTable(), $alias, $condition);
    }
}
