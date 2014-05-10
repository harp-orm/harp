<?php

namespace CL\Luna\Rel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait LoadFromDataTrait
{
    public function loadFromData(array $data)
    {
        if (isset($data['_id'])) {
            $store = $this->getForeignStore();

            return $store->find($data['_id']);
        }
    }
}
