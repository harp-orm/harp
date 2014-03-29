<?php namespace CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Boolean extends AbstractField
{
    public function load($value)
    {
        return (bool) $value;
    }

    public function save($value)
    {
        return (bool) $value;
    }
}
