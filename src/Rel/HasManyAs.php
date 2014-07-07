<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Repo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Rel\AbstractRelMany;
use Harp\Core\Rel\UpdateManyInterface;
use Harp\Query\AbstractWhere;
use Harp\Query\SQL\SQL;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyAs extends AbstractRelMany implements RelInterface, UpdateManyInterface
{
    protected $foreignKey;
    protected $foreignClassKey;

    public function __construct(
        $name,
        Repo $store,
        Repo $foreignRepo,
        $foreignKeyName,
        array $options = array()
    )
    {
        $this->foreignKey = $foreignKeyName.'Id';
        $this->foreignClassKey = $foreignKeyName.'Class';

        parent::__construct($name, $store, $foreignRepo, $options);
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getForeignClassKey()
    {
        return $this->foreignClassKey;
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
            ->whereIn($this->getForeignKey(), $keys)
            ->where($this->getForeignClassKey(), $this->getRepo()->getModelClass())
            ->loadRaw($flags);
    }

    /**
     * @param  AbstractModel $model
     * @param  AbstractModel $foreign
     * @return boolean
     */
    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return (
            $model->{$this->getKey()} == $foreign->{$this->getForeignKey()}
            and $this->getRepo()->getModelClass() == $foreign->{$this->getForeignClassKey()}
        );
    }

    /**
     * @param  AbstractWhere $query
     * @param  string        $parent
     */
    public function join(AbstractWhere $query, $parent)
    {
        $alias = $this->getName();

        $conditions = [
            "$alias.{$this->getForeignKey()}" => "$parent.{$this->getKey()}",
            "$alias.{$this->getForeignClassKey()}" => new SQL('= ?', [$this->getRepo()->getModelClass()]),
        ];

        if ($this->getForeignRepo()->getSoftDelete()) {
            $conditions["$alias.deletedAt"] = new SQL('IS NULL');
        }

        $query->joinAliased($this->getForeignRepo()->getTable(), $alias, $conditions);
    }

    /**
     * @param  LinkMany      $link
     */
    public function update(LinkMany $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $link->getModel()->{$this->getKey()};
            $added->{$this->getForeignClassKey()} = $this->getRepo()->getModelClass();
        }

        foreach ($link->getRemoved() as $added) {
            $added->{$this->getForeignKey()} = null;
            $added->{$this->getForeignClassKey()} = null;
        }
    }
}
