<?php namespace CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class DateTime extends AbstractField
{
    public static $format = 'Y-m-d H:i:s';

    public function save($value)
    {
        if ( ! is_numeric($value)) {
            $value = strtotime($value);
        }

        return date(self::$format, $value);
    }
}
