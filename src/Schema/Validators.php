<?php namespace CL\Luna\Schema;

use CL\Luna\Validator\AbstractValidator;
use CL\Luna\Util\Collection;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Validators extends Collection {

	public function add(AbstractValidator $item)
	{
		$this->items[$item->getName()] []= $item;

		return $this;
	}

	public function execute($value, $name)
	{
		$validators = $this->get($name);

		return array_filter(array_map(function($validator) use ($name, $value) {
			return $validator->getError($key, $value);
		}, $validators));
	}

	public function executeArray(array $data)
	{
		$data = array_intersect_key($data, $this->items);

		array_walk($data, [$this, 'executeFor']);

		$errorItems = Arr::flatten($data);

		return new Errors($errorItems);
	}
}
