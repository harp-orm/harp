<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractRepo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Rel\AbstractRelOne;
use Harp\Core\Rel\UpdateOneInterface;
use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends AbstractRelOne implements RelInterface, UpdateOneInterface
{
    protected $foreignKey;

    public function __construct($name, AbstractRepo $repo, AbstractRepo $foreignRepo, array $options = array())
    {
        $this->foreignKey = lcfirst($repo->getName()).'Id';

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    /**
     * @param  Models  $models
     * @return boolean
     */
    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->getKey());
    }

    /**
     * @param  Models $models
     * @param  int    $flags
     * @return AbstractModelsp[]
     */
    public function loadForeign(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->getKey());

        return $this->getForeignRepo()
            ->findAll()
            ->whereIn($this->getForeignKey(), $keys)
            ->loadRaw($flags);
    }

    /**
     * @param  AbstractModel $model
     * @param  AbstractModel $foreign
     * @return boolean
     */
    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    public function update(LinkOne $link)
    {
        if ($link->isChanged())
        {
            $link->get()->{$this->getForeignKey()} = $link->getModel()->{$this->getKey()};
            $link->getOriginal()->{$this->getForeignKey()} = null;
        }
    }

    /**
     * @param  AbstractWhere $query
     * @param  string        $parent
     */
    public function join(AbstractWhere $query, $parent)
    {
        $alias = $this->getName();
        $condition = "ON $alias.{$this->getForeignKey()} = $parent.{$this->getKey()}";

        if ($this->getForeignRepo()->getSoftDelete()) {
            $condition .= " AND $alias.deletedAt IS NULL";
        }

        $query->joinAliased($this->getForeignRepo()->getTable(), $alias, $condition);
    }
}
