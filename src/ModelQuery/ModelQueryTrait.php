<?php namespace CL\Luna\ModelQuery;

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

    protected $Store;

    public function setStore(Store $Store)
    {
        $this->Store = $Store;
        $this->db = DB::get($Store->getDb());

        return $this;
    }

    public function getStore()
    {
        return $this->Store;
    }

    public function getRel($name)
    {
        return $this->Store->getRel($name);
    }

    public function addToLog()
    {
        if (Log::getEnabled())
        {
            Log::add($this->humanize());
        }
    }

    public function whereKey($key)
    {
        return $this->where($this->getStore()->getTable().'.'.$this->getStore()->getPrimaryKey(), $key);
    }

    public function whereKeys(array $keys)
    {
        return $this->where($this->getStore()->getTable().'.'.$this->getStore()->getPrimaryKey(), $keys);
    }

    public function joinRels($rels)
    {
        $rels = Arr::toAssoc((array) $rels);

        $this->joinNestedRels($this->getStore(), $rels, $this->getStore()->getTable());

        return $this;
    }

    public function joinNestedRels($Store, array $rels, $parent)
    {
        foreach ($rels as $name => $childRels)
        {
            $rel = $Store->getRel($name);

            if (! $rel) {
                throw new InvalidArgumentException(
                    sprintf('Relation %s does not exist on %s when joining', $name, $Store->getName())
                );
            }

            $rel->joinRel($this, $parent);

            if ($childRels)
            {
                $this->joinNestedRels($rel->getForeignStore(), $childRels, $name);
            }
        }
    }
}
