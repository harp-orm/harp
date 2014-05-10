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
            $Store = $this->getForeignStore();

            return $Store->find($data['_id']);
        }
    }
}
