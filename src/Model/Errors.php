<?php namespace CL\Luna\Model;

use CL\Luna\DB\DB;
use CL\Luna\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Errors{

	protected $errors = [];

	public function add(Error $error)
	{
		$this->errors[] = $error;
	}

	public function all()
	{
		return $this->errors;
	}

	public function isEmpty()
	{
		return empty($this->errors);
	}

	public function validateChanges(array $changes, array $validators)
	{
		$changes = array_intersect_key($changes, $validators);

		foreach ($changes as $attribute => $change)
		{
			foreach ($validators[$attribute] as $validator)
			{
				if (($error = $validator->getError($attribute, $change)))
				{
					$this->add($error);
				}
			}
		}
	}
}
