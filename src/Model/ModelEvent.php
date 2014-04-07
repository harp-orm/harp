<?php namespace CL\Luna\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class ModelEvent
{
    const BEFORE_INSERT = 1;
    const BEFORE_UPDATE = 3;
    const BEFORE_DELETE = 3;
    const BEFORE_PERSIST = 4;
    const BEFORE_VALIDATE = 5;

    const AFTER_INSERT = 6;
    const AFTER_UPDATE = 7;
    const AFTER_DELETE = 8;
    const AFTER_PERSIST = 9;
    const AFTER_VALIDATE = 10;

}
