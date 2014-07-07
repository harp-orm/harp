<?php

namespace Harp\Harp;

use Harp\Core\Save\AbstractFind;
use Harp\Core\Model\State;
use Harp\Query\SQL\SQL;
use PDO;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Find extends AbstractFind
{
    /**
     * @var Query\Select
     */
    private $select;

    public function __construct(Repo $repo)
    {
        $this->select = new Query\Select($repo);

        parent::__construct($repo);
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->getRepo()->getTable();
    }

    /**
     * @return array
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @return Find
     */
    public function setSelect(Query\Select $select)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $column
     * @param  string                      $alias
     * @return Find
     */
    public function column($column, $alias = null)
    {
        $this->select->column($column, $alias);

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $column
     * @param  string                      $alias
     * @return Find
     */
    public function prependColumn($column, $alias = null)
    {
        $this->select->prependColumn($column, $alias);

        return $this;
    }

    /**
     * @return Find
     */
    public function clearColumns()
    {
        $this->select->clearColumns();

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public function where($property, $value)
    {
        $this->select->where($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  array  $properties
     * @return Find   $this
     */
    public function whereRaw($sql, array $properties = array())
    {
        $this->select->whereRaw($sql, $properties);

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public function whereNot($property, $value)
    {
        $this->select->whereNot($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  array  $value
     * @return Find   $this
     */
    public function whereIn($property, array $value)
    {
        $this->select->whereIn($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  string $value
     * @return Find   $this
     */
    public function whereLike($property, $value)
    {
        $this->select->whereLike($property, $value);

        return $this;
    }

    /**
     * @return Find   $this
     */
    public function clearWhere()
    {
        $this->select->clearWhere();

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public function having($property, $value)
    {
        $this->select->having($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public function havingNot($property, $value)
    {
        $this->select->havingNot($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  array  $value
     * @return Find   $this
     */
    public function havingIn($property, array $value)
    {
        $this->select->havingIn($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public function havingLike($property, $value)
    {
        $this->select->havingLike($property, $value);

        return $this;
    }

    /**
     * @return Find   $this
     */
    public function clearHaving()
    {
        $this->select->clearHaving();

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $column
     * @param  string                      $direction
     * @return Find
     */
    public function group($column, $direction = null)
    {
        $this->select->group($column, $direction);

        return $this;
    }

    /**
     * @return Find
     */
    public function clearGroup()
    {
        $this->select->clearGroup();

        return $this;
    }

    /**
     * @param  string $column
     * @param  string $direction
     * @return Find
     */
    public function order($column, $direction = null)
    {
        $this->select->order($column, $direction);

        return $this;
    }

    /**
     * @return Find
     */
    public function clearOrder()
    {
        $this->select->clearOrder();

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $table
     * @param  string|array                $condition
     * @param  string                      $type
     * @return Find
     */
    public function join($table, $condition, $type = null)
    {
        $this->select->join($table, $condition, $type);

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $table
     * @param  string|array                $alias
     * @param  string|array                $condition
     * @param  string                      $type
     * @return Find
     */
    public function joinAliased($table, $alias, $condition, $type = null)
    {
        $this->select->joinAliased($table, $alias, $condition, $type);

        return $this;
    }

    /**
     * @param  array  $rels
     * @return Find   $this
     */
    public function joinRels(array $rels)
    {
        $this->select->joinRels($rels);

        return $this;
    }

    /**
     * @return Find   $this
     */
    public function clearJoin()
    {
        $this->select->clearJoin();

        return $this;
    }

    /**
     * @param  int  $limit
     * @return Find $this
     */
    public function limit($limit)
    {
        $this->select->limit($limit);

        return $this;
    }

    /**
     * @return Find $this
     */
    public function clearLimit()
    {
        $this->select->clearLimit();

        return $this;
    }

    /**
     * @param  int  $offset
     * @return Find $this
     */
    public function offset($offset)
    {
        $this->select->offset($offset);

        return $this;
    }

    /**
     * @return Find   $this
     */
    public function clearOffset()
    {
        $this->select->clearOffset();

        return $this;
    }

    /**
     * @return AbstractFind $this
     */
    public function applyFlags()
    {
        if ($this->getRepo()->getSoftDelete()) {
            if ($this->getFlags() === State::SAVED) {
                $this->where($this->getTable().'.deletedAt', null);
            } elseif ($this->getFlags() === State::DELETED) {
                $this->whereNot($this->getTable().'.deletedAt', null);
            }
        }

        return $this;
    }

    /**
     * @param  mixed        $value
     * @return AbstractFind $this
     */
    public function whereKey($value)
    {
        $property = $this->getRepo()->getPrimaryKey();

        $this->where($this->getTable().'.'.$property, $value);

        return $this;
    }

    /**
     * @return string $this
     */
    public function humanize()
    {
        return $this->select->humanize();
    }

    /**
     * @return string $this
     */
    public function sql()
    {
        return $this->select->sql();
    }

    /**
     * @return AbstractModel[]
     */
    public function execute()
    {
        if ($this->getRepo()->getInherited()) {
            $this->select->prependColumn($this->getTable().'.class');
            return $this->select->execute()->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
        } else {
            return $this->select->execute()->fetchAll(PDO::FETCH_CLASS, $this->getRepo()->getModelClass());
        }
    }

    /**
     * @param  int $flags
     * @return array
     */
    public function loadIds($flags = null)
    {
        $repo = $this->getRepo();

        $this->applyFlags($flags);

        $statement = $this->select
            ->clearColumns()
            ->column("{$repo->getTable()}.{$repo->getPrimaryKey()}", 'id')
            ->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN, 'id');
    }

    /**
     * @param  int $flags
     * @return int
     */
    public function loadCount($flags = null)
    {
        $repo = $this->getRepo();

        $this->applyFlags($flags);
        $table = $this->select->getDb()->escapeName($repo->getTable());
        $primaryKey = $this->select->getDb()->escapeName($repo->getPrimaryKey());

        $this->select
            ->clearColumns()
            ->column(new SQL("COUNT($table.$primaryKey)"), 'countAll');

        return $this->select->execute()->fetchColumn();
    }
}
