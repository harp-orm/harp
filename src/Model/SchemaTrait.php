<?php

namespace CL\Luna\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait SchemaTrait
{
    private static $instance;

    public static function get()
    {
        if (! self::$instance) {
            $class = get_called_class();

            self::$instance = new $class();
        }

        return self::$instance;
    }
}
