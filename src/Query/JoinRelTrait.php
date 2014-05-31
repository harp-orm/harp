<?php

namespace Harp\Harp\Query;

use Harp\Harp\AbstractRepo;
use Harp\Util\Arr;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait JoinRelTrait {

    public function joinRels($rels)
    {
        $rels = Arr::toAssoc((array) $rels);

        $this->joinNestedRels($this->getRepo(), $rels, $this->getRepo()->getTable());

        return $this;
    }

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
