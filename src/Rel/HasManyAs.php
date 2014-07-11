<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Config;
use Harp\Harp\Repo;
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\LinkMany;
use Harp\Query\AbstractWhere;
use Harp\Query\SQL\SQL;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyAs extends AbstractRelMany implements UpdateManyInterface
{
    protected $foreignKey;
    protected $foreignClassKey;

    public function __construct($name, Config $config, Repo $repo, $foreignKeyName, array $options = array())
    {
        $this->foreignKey = $foreignKeyName.'Id';
        $this->foreignClassKey = $foreignKeyName.'Class';

        parent::__construct($name, $config, $repo, $options);
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
        return $this->getConfig()->getPrimaryKey();
    }

    /**
     * @param  Models  $models
     * @return boolean
     */
    public function hasModels(Models $models)
    {
        return ! $models->isEmptyProperty($this->getKey());
    }

    /**
     * @param  Models $models
     * @param  int $flags
     * @return AbstractModel[]
     */
    public function loadModels(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->getKey());

        return $this
            ->findAllWhereIn($this->getForeignKey(), $keys, $flags)
            ->where($this->getForeignClassKey(), $this->getConfig()->getModelClass())
            ->loadRaw();
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
            and $this->getConfig()->getModelClass() == $foreign->{$this->getForeignClassKey()}
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
            "$alias.{$this->getForeignClassKey()}" => new SQL('= ?', [$this->getConfig()->getModelClass()]),
        ];

        $conditions += $this->getSoftDeleteConditions();

        $query->joinAliased($this->getRepo()->getTable(), $alias, $conditions);

    }

    /**
     * @param  LinkMany      $link
     */
    public function update(LinkMany $link)
    {
        foreach ($link->getAdded() as $added) {
            $added->{$this->getForeignKey()} = $link->getModel()->{$this->getKey()};
            $added->{$this->getForeignClassKey()} = $this->getConfig()->getModelClass();
        }

        foreach ($link->getRemoved() as $added) {
            $added->{$this->getForeignKey()} = null;
            $added->{$this->getForeignClassKey()} = null;
        }
    }
}
