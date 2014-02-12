<?php namespace CL\Luna\Validator;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractValidator {

	private $name;

	public function __construct($name, array $properties = NULL)
	{
		$this->name = $name;

		if ($properties)
		{
			foreach ($properties as $propertyName => $value)
			{
				$this->$propertyName = $value;
			}
		}
	}

	public function getName()
	{
		return $this->name;
	}

	abstract public function getError($attribute, $value);

}
