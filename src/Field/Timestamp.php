<?php namespace CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Timestamp extends AbstractField
{
    public function save($value)
    {
        if ( ! is_numeric($value)) {
            $value = strtotime($value);
        }

        return date('Y-m-d H:i:s', $value);
    }
}
