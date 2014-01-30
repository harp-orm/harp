<?php namespace CL\Luna\Validator;

use CL\Luna\Model\Instance;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractValidator {

	abstract public function getError($attribute, $value);

}
