<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Repo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkOne;
use Harp\Core\Rel\AbstractRelOne;
use Harp\Core\Rel\UpdateOneInterface;
use Harp\Query\AbstractWhere;
use Harp\Query\SQL\SQL;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsTo extends AbstractRelOne implements RelInterface, UpdateOneInterface
{
    /**
     * @var string
     */
    protected $key;

    public function __construct($name, Repo $store, Repo $foreignRepo, array $options = array())
    {
        $this->key = $name.'Id';

        parent::__construct($name, $store, $foreignRepo, $options);
    }

    /**
     * @param  Models  $models
     * @return boolean
     */
    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->key);
    }

    /**
     * @param  Models $models
     * @param  int $flags
     * @return AbstractModel[]
     */
    public function loadForeign(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->key);

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

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->getRepo()->getPrimaryKey();
    }

    /**
     * @param  LinkOne       $link
     */
    public function update(LinkOne $link)
    {
        $link->getModel()->{$this->getKey()} = $link->get()->getId();
    }

    /**
     * @param  AbstractWhere $query
     * @param  string        $parent
     */
    public function join(AbstractWhere $query, $parent)
    {
        $alias = $this->getName();
        $conditions = ["$alias.{$this->getForeignKey()}" => "$parent.{$this->getKey()}"];

        if ($this->getForeignRepo()->getSoftDelete()) {
            $conditions["$alias.deletedAt"] = new SQL('IS NULL');
        }

        $query->joinAliased($this->getForeignRepo()->getTable(), $this->getName(), $conditions);
    }

}
