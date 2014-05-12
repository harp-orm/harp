<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Model\AbstractDbRepo;
use CL\Luna\Util\Arr;
use CL\Atlas\DB;
use InvalidArgumentException;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait ModelQueryTrait {

    protected $repo;

    public function setRepo(AbstractDbRepo $repo)
    {
        $this->repo = $repo;
        $this->db = DB::get($repo->getDb());

        return $this;
    }

    public function getRepo()
    {
        return $this->repo;
    }

    public function getRel($name)
    {
        return $this->repo->getRel($name);
    }

    public function whereKey($key)
    {
        return $this->where($this->repo->getTable().'.'.$this->repo->getPrimaryKey(), $key);
    }

    public function whereKeys(array $keys)
    {
        return $this->where($this->repo->getTable().'.'.$this->repo->getPrimaryKey(), $keys);
    }

    public function joinRels($rels)
    {
        $rels = Arr::toAssoc((array) $rels);

        $this->joinNestedRels($this->repo, $rels, $this->repo->getTable());

        return $this;
    }

    public function joinNestedRels(AbstractDbRepo $repo, array $rels, $parent)
    {
        foreach ($rels as $name => $childRels)
        {
            $rel = $repo->getRel($name);

            if (! $rel) {
                throw new InvalidArgumentException(
                    sprintf('Relation %s does not exist on %s when joining', $name, $repo->getName())
                );
            }

            $rel->joinRel($this, $parent);

            if ($childRels) {
                $this->joinNestedRels($rel->getForeignRepo(), $childRels, $name);
            }
        }
    }
}
