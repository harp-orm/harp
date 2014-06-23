<?php

namespace Harp\Harp\Query;

use Harp\Core\Repo\AbstractRepo;
use Harp\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait JoinRelTrait
{
    /**
     * @return AbstractRepo
     */
    abstract public function getRepo();

    /**
     * @param  array|string $rels
     */
    public function joinRels($rels)
    {
        $rels = Arr::toAssoc((array) $rels);

        $this->joinNestedRels($this->getRepo(), $rels, $this->getRepo()->getTable());

        return $this;
    }

    /**
     * @param  AbstractRepo $repo
     * @param  array        $rels
     * @param  string       $parent
     */
    private function joinNestedRels(AbstractRepo $repo, array $rels, $parent)
    {
        foreach ($rels as $name => $childRels)
        {
            $rel = $repo->getRelOrError($name);

            $rel->join($this, $parent);

            if ($childRels) {
                $this->joinNestedRels($rel->getForeignRepo(), $childRels, $name);
            }
        }
    }
}
