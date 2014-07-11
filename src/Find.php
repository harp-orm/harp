<?php

namespace Harp\Harp;

use Harp\Harp\Model\State;
use Harp\Harp\Repo\RepoModels;
use Harp\Harp\Query\SelectProxyTrait;
use Harp\Harp\Query\Select;
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
    use SelectProxyTrait;

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
        $this->select = new Select($repo);
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
     * @return Query\Select
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
     * Use the Primary key for the "name" part of the where constraint
     *
     * @param  mixed        $value
     * @return Find $this
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
     * @return Find $this
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
     * @return Find $this
     */
    public function deletedAndSaved()
    {
        $this->setFlags(State::DELETED | State::SAVED);

        return $this;
    }

    /**
     * Add a constrint to only return soft deleted models
     *
     * @return Find $this
     */
    public function onlyDeleted()
    {
        $this->setFlags(State::DELETED);

        return $this;
    }

    /**
     * Add a constrint to only return models that are not soft deleted
     *
     * @return Find $this
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
     * @return Find $this
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
     * @return Find $this
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
     * @return string
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
