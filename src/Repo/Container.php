<?php

namespace Harp\Harp\Repo;

use Harp\Harp\Repo;
use Harp\Harp\Config;
use InvalidArgumentException;

/**
 * A dependancy injection container for Repo objects
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Container
{
    /**
     * Holds all the singleton repo instances.
     * Use the name of the class as array key.
     *
     * @var array
     */
    private static $repos;

    /**
     * @var array
     */
    private static $actualClasses;

    /**
     * @var string
     */
    private static $configClass;

    /**
     * @param  string $class
     * @return Repo
     */
    public static function get($class)
    {
        if (! self::has($class)) {
            if (self::hasActualClass($class)) {
                $actualClass = self::getActualClass($class);

                if (self::has($actualClass)) {
                    $repo = self::get($actualClass);
                } else {
                    $repo = self::newRepo($actualClass);
                }

                self::set($actualClass, $repo);
            } else {
                $repo = self::newRepo($class);
            }

            self::set($class, $repo);
        }

        return self::$repos[$class];
    }

    public static function newRepo($class)
    {
        if (self::$configClass) {
            $configClass = self::$configClass;
            return new Repo(new $configClass($class));
        } else {
            return new Repo(new Config($class));
        }
    }

    public static function setConfigClass($class)
    {
        if (! is_subclass_of($class, 'Harp\Harp\Config')) {
            throw new InvalidArgumentException(
                sprintf('Config class %s must be a subclass of Harp\Harp\Config', $class)
            );
        }

        self::$configClass = $class;
    }

    /**
     * @param string $class
     * @param Repo   $repo
     */
    public static function set($class, Repo $repo)
    {
        self::$repos[$class] = $repo;
    }

    /**
     * @param string $class
     * @param string $alias
     */
    public static function setActualClass($class, $alias)
    {
        self::$actualClasses[$class] = $alias;
    }

    /**
     * Set multiple actual classes at once. [class => actual class]
     *
     * @param array $actual
     */
    public static function setActualClasses(array $actual)
    {
        foreach ($actual as $class => $actualClass) {
            self::setActualClass($class, $actualClass);
        }
    }

    /**
     * @param  string $class
     * @return string
     */
    public static function getActualClass($class)
    {
        return self::$actualClasses[$class];
    }

    /**
     * @param  string  $class
     * @return boolean
     */
    public static function hasActualClass($class)
    {
        return isset(self::$actualClasses[$class]);
    }

    /**
     * @param  string  $class
     * @return boolean
     */
    public static function has($class)
    {
        return isset(self::$repos[$class]);
    }

    public static function clear()
    {
        self::$configClass = null;
        self::$repos = [];
        self::$actualClasses = [];
    }
}
