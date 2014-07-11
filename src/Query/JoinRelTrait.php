<?php

namespace Harp\Harp\Query;

use Harp\Harp\Repo;
use Harp\Util\Arr;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait JoinRelTrait
{
    /**
     * @return Repo
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
     * @param  Repo $repo
     * @param  array        $rels
     * @param  string       $parent
     */
    private function joinNestedRels(Repo $repo, array $rels, $parent)
    {
        foreach ($rels as $name => $childRels)
        {
            $rel = $repo->getRelOrError($name);

            $rel->join($this, $parent);

            if ($childRels) {
                $this->joinNestedRels($rel->getRepo(), $childRels, $name);
            }
        }
    }
}
