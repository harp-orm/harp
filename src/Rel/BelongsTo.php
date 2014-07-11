<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Repo;
use Harp\Harp\Config;
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\LinkOne;
use Harp\Query\AbstractWhere;
use Harp\Query\SQL\SQL;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsTo extends AbstractRelOne implements UpdateOneInterface
{
    /**
     * @var string
     */
    protected $key;

    public function __construct($name, Config $config, Repo $repo, array $options = array())
    {
        $this->key = $name.'Id';

        parent::__construct($name, $config, $repo, $options);
    }

    /**
     * @param  Models  $models
     * @return boolean
     */
    public function hasModels(Models $models)
    {
        return ! $models->isEmptyProperty($this->key);
    }

    /**
     * @param  Models $models
     * @param  int $flags
     * @return AbstractModel[]
     */
    public function loadModels(Models $models, $flags = null)
    {
        $keys = $models->pluckPropertyUnique($this->key);

        return $this->getRepo()
            ->findAll()
            ->whereIn($this->getForeignKey(), $keys)
            ->setFlags($flags)
            ->loadRaw();
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
        return $this->getConfig()->getPrimaryKey();
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

        if ($this->getRepo()->getSoftDelete()) {
            $conditions["$alias.deletedAt"] = new SQL('IS NULL');
        }

        $query->joinAliased($this->getRepo()->getTable(), $this->getName(), $conditions);
    }

}
