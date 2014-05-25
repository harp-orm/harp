<?php

namespace CL\Luna\Rel;

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
class BelongsTo extends AbstractRelOne implements DbRelInterface, UpdateOneInterface
{
    protected $key;

    public function __construct($name, AbstractDbRepo $store, AbstractDbRepo $foreignRepo, array $options = array())
    {
        $this->key = $name.'Id';

        parent::__construct($name, $store, $foreignRepo, $options);
    }

    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->key);
    }

    public function loadForeign(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->key);

        return $this->getForeignRepo()
            ->findAll()
            ->whereIn($this->getForeignKey(), $keys)
            ->loadRaw($flags);
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getForeignKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    public function update(AbstractModel $model, LinkOne $link)
    {
        $model->{$this->getKey()} = $link->get()->getId();
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $alias = $this->getName();
        $condition = "ON $alias.{$this->getForeignKey()} = $parent.{$this->getKey()}";

        if ($this->getForeignRepo()->getSoftDelete()) {
            $condition .= " AND $alias.deletedAt IS NULL";
        }

        $query->joinAliased($this->getForeignTable(), $alias, $condition);
    }

}
