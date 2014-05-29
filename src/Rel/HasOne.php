<?php

namespace Harp\Db\Rel;

use CL\Util\Arr;
use Harp\Db\AbstractDbRepo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Rel\AbstractRelOne;
use Harp\Core\Rel\UpdateOneInterface;
use Harp\Query\AbstractQuery;
/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends AbstractRelOne implements DbRelInterface, UpdateOneInterface
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

    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->getKey());
    }

    public function loadForeign(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->getKey());

        return $this->getForeignRepo()
            ->findAll()
            ->whereIn($this->getForeignKey(), $keys)
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

        $query->joinAliased($this->getForeignRepo()->getTable(), $alias, $condition);
    }
}
