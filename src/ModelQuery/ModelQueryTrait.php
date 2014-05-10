<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Model\Store;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Log;
use CL\Atlas\DB;
use InvalidArgumentException;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait ModelQueryTrait {

    protected $store;

    public function setStore(Store $store)
    {
        $this->store = $store;
        $this->db = DB::get($store->getDb());

        return $this;
    }

    public function getStore()
    {
        return $this->store;
    }

    public function getRel($name)
    {
        return $this->store->getRel($name);
    }

    public function addToLog()
    {
        if (Log::getEnabled()) {
            Log::add($this->humanize());
        }
    }

    public function whereKey($key)
    {
        return $this->where($this->store->getTable().'.'.$this->store->getPrimaryKey(), $key);
    }

    public function whereKeys(array $keys)
    {
        return $this->where($this->store->getTable().'.'.$this->store->getPrimaryKey(), $keys);
    }

    public function joinRels($rels)
    {
        $rels = Arr::toAssoc((array) $rels);

        $this->joinNestedRels($this->store, $rels, $this->store->getTable());

        return $this;
    }

    public function joinNestedRels(Store $store, array $rels, $parent)
    {
        foreach ($rels as $name => $childRels)
        {
            $rel = $store->getRel($name);

            if (! $rel) {
                throw new InvalidArgumentException(
                    sprintf('Relation %s does not exist on %s when joining', $name, $store->getName())
                );
            }

            $rel->joinRel($this, $parent);

            if ($childRels) {
                $this->joinNestedRels($rel->getForeignStore(), $childRels, $name);
            }
        }
    }
}
