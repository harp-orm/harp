<?php

namespace Harp\Harp\Query;

use Harp\Harp\Config;
use Harp\Harp\Session;
use Harp\Harp\SelectLoader;
use Harp\Harp\SelectEagerLoader;
use Harp\Harp\Models;
use Harp\Query\SQL\SQL;
use Harp\Query\DB;
use PDO;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Select extends \Harp\Query\Select
{
    use JoinRelTrait;

    /**
     * @var Config
     */
    private $config;
    private $sessionInstanceId;

    public function __construct(Session $session, Config $config)
    {
        parent::__construct($session->getDb());

        $this->sessionInstanceId = $session->getInstanceId();

        $this->config = $config;

        $table = $this->getDb()->escapeName($config->getTable());

        $this
            ->from($config->getTable())
            ->column(new SQL("{$table}.*"));

        if ($config->isSoftDelete()) {
            $this->where($config->getTable().'.'.Config::SOFT_DELETE_KEY, null);
        }

        if ($config->isInherited()) {
            $this->prependColumn($config->getTable().'.'.Config::INHERITED_KEY);
        }
    }

    public function getSession()
    {
        return Session::getInstance($this->sessionInstanceId);
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Use the Primary key for the "name" part of the where constraint
     *
     * @param  mixed        $value
     * @return Find $this
     */
    public function whereKey($value)
    {
        $config = $this->getConfig();

        $this->select->where($config->getTable().Config::PRIMARY_KEY, $value);

        return $this;
    }

    /**
     * Use the Primary key for the "name" part of the where constraint
     *
     * @param  mixed        $values
     * @return Find $this
     */
    public function whereKeys(array $values)
    {
        $config = $this->getConfig();

        $this->select->whereIn($config->getTable().Config::PRIMARY_KEY, $values);

        return $this;
    }

    public function execute()
    {
        $statement = parent::execute();

        if ($this->getConfig()->isInherited()) {
            $statement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
        } else {
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->getConfig()->getModelClass());
        }

        return $statement;
    }

    /**
     * Calls "execute" and passes the result through an IdentityMap
     *
     * @return RepoModels
     */
    public function getModels()
    {
        return new Models(new SelectLoader($this));
    }

    /**
     * Eager load relations.
     *
     * Example:
     *   ->loadWith(['user' => 'profile'])
     *
     * @param  array      $rels
     * @return RepoModels
     */
    public function getModelsWith(array $rels)
    {
        return new Models(new SelectEagerLoader($this, $rels));
    }

    /**
     * Will return a void model if no model is found.
     *
     * @return Model
     */
    public function fetchFirst()
    {
        return $this->limit(1)->getModels()->getFirst();
    }

    /**
     * @param  int $flags
     * @return array
     */
    public function fetchIds()
    {
        $config = $this->getConfig();
        $select = clone $this;

        return $select
            ->clearColumns()
            ->column($config->getTable().'.'.Config::PRIMARY_KEY, 'id')
            ->execute()
            ->fetchAll(PDO::FETCH_COLUMN, 'id');
    }

    /**
     * @param  int $flags
     * @return string
     */
    public function fetchCount()
    {
        $select = clone $this;

        return $select
            ->clearColumns()
            ->column(new SQL("COUNT(*)"), 'countAll')
            ->execute()
            ->fetchColumn();
    }
}
