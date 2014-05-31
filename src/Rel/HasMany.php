<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractDbRepo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Rel\AbstractRelMany;
use Harp\Core\Rel\UpdateManyInterface;
use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasMany extends AbstractRelMany implements DbRelInterface, UpdateManyInterface
{
    protected $foreignKey;

    public function __construct($name, AbstractDbRepo $repo, AbstractDbRepo $foreignRepo, array $options = array())
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
     * @param  int $flags
     * @return AbstractModel[]
     */
    public function loadForeign(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->getKey());

        return $this->getForeignRepo()
            ->findAll()
            ->whereIn($this->foreignKey, $keys)
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

    /**
     * @param  AbstractModel $model
     * @param  LinkMany      $link
     */
    public function update(AbstractModel $model, LinkMany $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $model->{$this->getKey()};
        }

        foreach ($link->getRemoved() as $added) {
            $added->{$this->getForeignKey()} = null;
        }
    }
}
