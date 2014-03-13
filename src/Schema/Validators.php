<?php namespace CL\Luna\Schema;

use CL\Luna\Validator\AbstractValidator;
use CL\Luna\Util\Collection;
use CL\Luna\Util\Arr;
use CL\Luna\Model\Errors;

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

    public function execute( & $value, $name)
    {
        $validators = $this->get($name);

        $value = array_filter(array_map(function($validator) use ($name, $value) {
            return $validator->getError($name, $value);
        }, $validators));
    }

    public function executeArray(array $data)
    {
        $data = array_intersect_key($data, $this->items);

        array_walk($data, [$this, 'execute']);

        $errorItems = Arr::flatten($data);

        return new Errors($errorItems);
    }
}
