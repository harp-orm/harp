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
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function where($property, $value)
    {
        return static::getRepoStatic()->findAll()->where($property, $value);
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function whereRaw($property, $value)
    {
        return static::getRepoStatic()->findAll()->whereRaw($property, $value);
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public static function whereNot($property, $value)
    {
        return static::getRepoStatic()->findAll()->whereNot($property, $value);
    }

    /**
     * @param  string $property
     * @param  array  $value
     * @return Find   $this
     */
    public static function whereIn($property, array $values)
    {
        return static::getRepoStatic()->findAll()->whereIn($property, $values);
    }

    /**
     * @param  string $property
     * @param  string $value
     * @return Find   $this
     */
    public static function whereLike($property, $value)
    {
        return static::getRepoStatic()->findAll()->whereLike($property, $value);
    }
}
