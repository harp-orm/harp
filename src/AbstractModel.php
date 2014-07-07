<?php

namespace Harp\Harp;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractModel extends \Harp\Core\Model\AbstractModel
{
    /**
     * @return Find
     */
    public static function findAll()
    {
        return new Find(self::getRepo());
    }

    /**
     * @param  string $class
     * @return Repo
     */
    public static function newRepo($class)
    {
        return new Repo($class);
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function where($property, $value)
    {
        return static::findAll()->where($property, $value);
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function whereRaw($property, $value)
    {
        return static::findAll()->whereRaw($property, $value);
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function whereNot($property, $value)
    {
        return static::findAll()->whereNot($property, $value);
    }

    /**
     * @param  string $property
     * @param  array  $value
     * @return Find   $this
     */
    public static function whereIn($property, array $values)
    {
        return static::findAll()->whereIn($property, $values);
    }

    /**
     * @param  string $property
     * @param  string $value
     * @return Find   $this
     */
    public static function whereLike($property, $value)
    {
        return static::findAll()->whereLike($property, $value);
    }
}
