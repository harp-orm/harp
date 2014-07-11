<?php

namespace Harp\Harp\Query;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait SelectProxyTrait
{
    /**
     * @return Select
     */
    abstract public function getSelect();

    /**
     * @param  string  $column
     * @param  string                      $alias
     * @return static
     */
    public function column($column, $alias = null)
    {
        $this->getSelect()->column($column, $alias);

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $column
     * @param  string                      $alias
     * @return static
     */
    public function prependColumn($column, $alias = null)
    {
        $this->getSelect()->prependColumn($column, $alias);

        return $this;
    }

    /**
     * @return static
     */
    public function clearColumns()
    {
        $this->getSelect()->clearColumns();

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return static
     */
    public function where($property, $value)
    {
        $this->getSelect()->where($property, $value);

        return $this;
    }

    /**
     * @param  array  $properties
     * @param string $sql
     * @return static
     */
    public function whereRaw($sql, array $properties = array())
    {
        $this->getSelect()->whereRaw($sql, $properties);

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return static
     */
    public function whereNot($property, $value)
    {
        $this->getSelect()->whereNot($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  array  $value
     * @return static
     */
    public function whereIn($property, array $value)
    {
        $this->getSelect()->whereIn($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  string $value
     * @return static
     */
    public function whereLike($property, $value)
    {
        $this->getSelect()->whereLike($property, $value);

        return $this;
    }

    /**
     * @return static
     */
    public function clearWhere()
    {
        $this->getSelect()->clearWhere();

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return static
     */
    public function having($property, $value)
    {
        $this->getSelect()->having($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return static
     */
    public function havingNot($property, $value)
    {
        $this->getSelect()->havingNot($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  array  $value
     * @return static
     */
    public function havingIn($property, array $value)
    {
        $this->getSelect()->havingIn($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return static
     */
    public function havingLike($property, $value)
    {
        $this->getSelect()->havingLike($property, $value);

        return $this;
    }

    /**
     * @return static
     */
    public function clearHaving()
    {
        $this->getSelect()->clearHaving();

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $column
     * @param  string                      $direction
     * @return static
     */
    public function group($column, $direction = null)
    {
        $this->getSelect()->group($column, $direction);

        return $this;
    }

    /**
     * @return static
     */
    public function clearGroup()
    {
        $this->getSelect()->clearGroup();

        return $this;
    }

    /**
     * @param  string $column
     * @param  string $direction
     * @return static
     */
    public function order($column, $direction = null)
    {
        $this->getSelect()->order($column, $direction);

        return $this;
    }

    /**
     * @return static
     */
    public function clearOrder()
    {
        $this->getSelect()->clearOrder();

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $table
     * @param  string|array                $condition
     * @param  string                      $type
     * @return static
     */
    public function join($table, $condition, $type = null)
    {
        $this->getSelect()->join($table, $condition, $type);

        return $this;
    }

    /**
     * @param  string|\Harp\Query\SQL\SQL  $table
     * @param  string|array                $alias
     * @param  string|array                $condition
     * @param  string                      $type
     * @return static
     */
    public function joinAliased($table, $alias, $condition, $type = null)
    {
        $this->getSelect()->joinAliased($table, $alias, $condition, $type);

        return $this;
    }

    /**
     * @param  array  $rels
     * @return static
     */
    public function joinRels(array $rels)
    {
        $this->getSelect()->joinRels($rels);

        return $this;
    }

    /**
     * @return static
     */
    public function clearJoin()
    {
        $this->getSelect()->clearJoin();

        return $this;
    }

    /**
     * @param  int  $limit
     * @return static
     */
    public function limit($limit)
    {
        $this->getSelect()->limit($limit);

        return $this;
    }

    /**
     * @return static
     */
    public function clearLimit()
    {
        $this->getSelect()->clearLimit();

        return $this;
    }

    /**
     * @param  int  $offset
     * @return static
     */
    public function offset($offset)
    {
        $this->getSelect()->offset($offset);

        return $this;
    }

    /**
     * @return static
     */
    public function clearOffset()
    {
        $this->getSelect()->clearOffset();

        return $this;
    }

}
