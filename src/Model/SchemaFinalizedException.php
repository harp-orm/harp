<?php namespace CL\Luna\Model;

use Exception;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class SchemaFinalizedException extends Exception {

	protected $message = 'The model is already initialized so you cannot modify it further';
}
