<?php namespace CL\Luna\ModelQuery;

use CL\Luna\Model\Store;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait SoftDeleteTrait {

    public $softDelete;

    public function setSoftDelete($softDelete)
    {
        $this->softDelete = (bool) $softDelete;

        return $this;
    }

    public function getSoftDelete()
    {
        return $this->softDelete;
    }

    public function applySoftDelete()
    {
        if ($this->getSoftDelete()) {
            $this->where($this->getStore()->getTable().'.'.Store::SOFT_DELETE_KEY, null);
        }
    }
}
