<?php

namespace Harp\Harp;

use Harp\Harp\Model\State;
use Harp\Harp\Repo\RepoModels;
use Harp\Harp\Query;
use Harp\Query\SQL\SQL;
use InvalidArgumentException;
use PDO;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Find
{
    /**
     * @var Query\Select
     */
    private $select;

    /**
     * @var int
     */
    private $flags = State::SAVED;

    public function __construct(Repo $repo)
    {
        $this->select = new Query\Select($repo);
    }

    /**
     * @return Repo
     */
    public function getRepo()
    {
        return $this->select->getRepo();
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
     * Use the Primary key for the "name" part of the where constraint
     *
     * @param  mixed        $value
     * @return AbstractFind $this
     */
    public function whereKey($value)
    {
        $repo = $this->getRepo();

        $this->select->where("{$repo->getTable()}.{$repo->getPrimaryKey()}", $value);

        return $this;
    }

    /**
     * Use the Primary key for the "name" part of the where constraint
     *
     * @param  mixed        $values
     * @return AbstractFind $this
     */
    public function whereKeys(array $values)
    {
        $repo = $this->getRepo();

        $this->select->whereIn("{$repo->getTable()}.{$repo->getPrimaryKey()}", $values);

        return $this;
    }

    /**
     * Add a constrint to return both soft deleted and saved models
     *
     * @return AbstractFind $this
     */
    public function deletedAndSaved()
    {
        $this->setFlags(State::DELETED | State::SAVED);

        return $this;
    }

    /**
     * Add a constrint to only return soft deleted models
     *
     * @return AbstractFind $this
     */
    public function onlyDeleted()
    {
        $this->setFlags(State::DELETED);

        return $this;
    }

    /**
     * Add a constrint to only return models that are not soft deleted
     *
     * @return AbstractFind $this
     */
    public function onlySaved()
    {
        $this->setFlags(State::SAVED);

        return $this;
    }

    /**
     * You can pass State::DELETED to retrieve only deleted
     * and State::DELETED | State::SAVED to retrieve deleted + saved
     *
     * @param  int          $flags
     * @return AbstractFind $this
     */
    public function setFlags($flags)
    {
        if ($flags !== null) {
            if (! in_array($flags, [State::SAVED, State::DELETED, State::DELETED | State::SAVED], true)) {
                $message = 'Flags were %s, but need to be State::SAVED, State::DELETED or State::DELETED | State::SAVED';
                throw new InvalidArgumentException(sprintf($message, $flags));
            }

            $this->flags = $flags;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getFlags()
    {
        return $this->flags;
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
     * @return AbstractModel[]
     */
    public function loadRaw()
    {
        $models = $this->applyFlags()->execute();

        return $models;
    }

    /**
     * Calls "loadRaw" and passes the result through an IdentityMap
     *
     * @return RepoModels
     */
    public function load()
    {
        $models = $this->loadRaw();

        foreach ($models as & $model) {
            $model = $model->getRepo()->getIdentityMap()->get($model);
        }

        return new RepoModels($this->getRepo(), $models);
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
    public function loadWith(array $rels)
    {
        $models = $this->load();

        $this->getRepo()->loadAllRelsFor($models, $rels, $this->flags);

        return $models;
    }

    /**
     * Will return a void model if no model is found.
     *
     * @return AbstractModel
     */
    public function loadFirst()
    {
        return $this->limit(1)->load()->getFirst();
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
        $db = $this->select->getDb();

        $this->applyFlags($flags);
        $table = $db->escapeName($repo->getTable());
        $primaryKey = $db->escapeName($repo->getPrimaryKey());

        $this->select
            ->clearColumns()
            ->column(new SQL("COUNT($table.$primaryKey)"), 'countAll');

        return $this->select->execute()->fetchColumn();
    }
}
