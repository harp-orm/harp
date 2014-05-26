<?php

namespace CL\Luna;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\State;
use CL\LunaCore\Save\AbstractFind;
use PDO;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Find extends AbstractFind
{
    /**
     * @var Select
     */
    private $select;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->select = new Query\Select($repo);

        parent::__construct($repo);
    }

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
     * @param  string $property
     * @param  string  $alias
     */
    public function column($column, $alias = null)
    {
        $this->select->column($column, $alias);

        return $this;
    }

    public function prependColumn($column, $alias = null)
    {
        $this->select->prependColumn($column, $alias);

        return $this;
    }

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

    public function clearHaving()
    {
        $this->select->clearHaving();

        return $this;
    }

    public function group($column, $direction = null)
    {
        $this->select->group($column, $direction);

        return $this;
    }

    public function clearGroup()
    {
        $this->select->clearGroup();

        return $this;
    }

    public function order($column, $direction = null)
    {
        $this->select->order($column, $direction);

        return $this;
    }

    public function clearOrder()
    {
        $this->select->clearOrder();

        return $this;
    }

    public function join($table, $condition, $type = null)
    {
        $this->select->join($table, $condition, $type);

        return $this;
    }

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

    public function clearOffset()
    {
        $this->select->clearOffset();

        return $this;
    }

    public function clearWhere()
    {
        $this->select->clearWhere();

        return $this;
    }

    public function onlySaved()
    {
        $this->where($this->getTable().'.deletedAt', null);

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

    public function onlyDeleted()
    {
        $this->whereNot($this->getTable().'.deletedAt', null);

        return $this;
    }

    public function humanize()
    {
        return $this->select->humanize();
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

    public function loadCount($flags = null)
    {
        $repo = $this->getRepo();

        $this->applyFlags($flags);

        $this->select
            ->clearColumns()
            ->column("COUNT({$repo->getTable()}.{$repo->getPrimaryKey()})", 'countAll');

        return $this->select->execute()->fetchColumn();
    }
}
