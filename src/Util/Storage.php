<?php namespace CL\Luna\Util;

use SplObjectStorage;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Storage
{
    public function invoke(SplObjectStorage $storage, $function_name)
    {
        $mapped = [];

        foreach ($storage as $object)
        {
            $mapped []= $object->$function_name();
        }

        return $mapped;
    }
}
