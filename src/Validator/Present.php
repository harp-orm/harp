<?php namespace CL\Luna\Validator;

use CL\Luna\Model\Error;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Present extends AbstractValidator
{
	const IDENTIFIER = 'present';

	public function getError($attribute, $value)
	{
		if ( ! $value)
		{
			return new Error(self::IDENTIFIER, $attribute);
		}
	}
}
