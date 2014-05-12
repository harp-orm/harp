<?php

namespace CL\Luna\Util;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Util
{
    public static function getPublicProperties($object)
    {
        return get_object_vars($object);
    }
}
