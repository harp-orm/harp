<?php

namespace Harp\Harp\Model;

use Harp\Harp\Repo;
use Harp\Harp\Repo\Container;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Save;
use Harp\Harp\Find;
use Harp\Harp\Query;
use Harp\Harp\AbstractModel;
use LogicException;

/**
 * Gives the model methods for accessing the corresponding repo
 * As well as a static interface for loading / saving models
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait RepoTrait
{
    /**
     * @return Repo
     */
    public static function getRepo()
    {
        return Container::get(get_called_class());
    }

    /**
     * Get the primaryKey from the repo
     *
     * @return string
     */
    public static function getPrimaryKey()
    {
        return self::getRepo()->getPrimaryKey();
    }

    /**
     * Get the nameKey from the repo
     *
     * @return string
     */
    public static function getNameKey()
    {
        return self::getRepo()->getNameKey();
    }

    /**
     * @param  string|int    $id
     * @param  int           $flags
     * @return AbstractModel
     */
    public static function find($id, $flags = null)
    {
        return self::findAll()
            ->where(self::getPrimaryKey(), $id)
            ->setFlags($flags)
            ->loadFirst();
    }

    /**
     * @param  string        $name
     * @param  int           $flags
     * @return AbstractModel
     */
    public static function findByName($name, $flags = null)
    {
        return self::findAll()
            ->where(self::getNameKey(), $name)
            ->setFlags($flags)
            ->loadFirst();
    }

    /**
     * @return Find
     */
    public static function findAll()
    {
        return new Find(self::getRepo());
    }

    public static function selectAll()
    {
        return new Query\Select(self::getRepo());
    }

    public static function deleteAll()
    {
        return new Query\Delete(self::getRepo());
    }

    public static function updateAll()
    {
        return new Query\Update(self::getRepo());
    }

    public static function insertAll()
    {
        return new Query\Insert(self::getRepo());
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function where($property, $value)
    {
        return self::findAll()->where($property, $value);
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function whereRaw($property, $value)
    {
        return self::findAll()->whereRaw($property, $value);
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function whereNot($property, $value)
    {
        return self::findAll()->whereNot($property, $value);
    }

    /**
     * @param  string $property
     * @param  array  $values
     * @return Find   $this
     */
    public static function whereIn($property, array $values)
    {
        return self::findAll()->whereIn($property, $values);
    }

    /**
     * @param  string $property
     * @param  string $value
     * @return Find   $this
     */
    public static function whereLike($property, $value)
    {
        return self::findAll()->whereLike($property, $value);
    }

    /**
     * Persist the model in the database
     *
     * @param AbstractModel $model
     */
    public static function save(AbstractModel $model)
    {
        (new Save())
            ->add($model)
            ->execute();
    }

    /**
     * Persist an array of models in the database
     *
     * @param AbstractModel[] $models
     */
    public static function saveArray(array $models)
    {
        (new Save())
            ->addArray($models)
            ->execute();
    }

    /**
     * Property defined by Repo Primary Key
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->{self::getPrimaryKey()};
    }

    /**
     * Set property defined by Repo Primary Key
     *
     * @param  mixed
     */
    public function setId($id)
    {
        $this->{self::getPrimaryKey()} = $id;

        return $this;
    }

    /**
     * Shortcut method to Repo's loadLink
     *
     * @param  string                       $name
     * @return Repo\AbstractLink
     */
    public function getLink($name)
    {
        return self::getRepo()->loadLink($this, $name);
    }

    /**
     * @param  string  $name
     * @return LinkOne
     */
    public function getLinkOne($name)
    {
        $link = $this->getLink($name);

        if (! $link instanceof LinkOne) {
            $message = 'Rel %s in %s must be a link of a BelongsTo, HasOne or other AbstractRelOne';
            throw new LogicException(sprintf($message, $name, get_class($this)));
        }

        return $link;
    }

    /**
     * @param  string        $name
     * @return AbstractModel
     */
    public function get($name)
    {
        return $this->getLinkOne($name)->get();
    }

    /**
     * @param string        $name
     * @param AbstractModel $model
     */
    public function set($name, AbstractModel $model)
    {
        $this->getLinkOne($name)->set($model);

        return $this;
    }

    /**
     * @param  string   $name
     * @return LinkMany
     */
    public function all($name)
    {
        $link = $this->getLink($name);

        if (! $link instanceof LinkMany) {
            $message = 'Rel %s in %s must be a link of a HasMany, HasManyThrough or other AbstractRelMany';
            throw new LogicException(sprintf($message, $name, get_class($this)));
        }

        return $link;
    }
}
