<?php

namespace Harp\Harp\Query;

use Harp\Harp\Config;
use Harp\Util\Arr;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait JoinRelTrait
{
    /**
     * @return Config
     */
    abstract public function getConfig();

    // public function joinRels($rels)
    // {
    //     $rels = Arr::toAssoc((array) $rels);

    //     $this->joinNestedRels($this->getConfig(), $rels, $this->getConfig()->getTable());

    //     return $this;
    // }

    // private function joinNestedRels(Config $config, array $rels, $parent)
    // {
    //     foreach ($rels as $name => $childRels)
    //     {
    //         $rel = $config->getRelOrError($name);

    //         $rel->join($this, $parent);

    //         if ($childRels) {
    //             $this->joinNestedRels($rel->getConfig(), $childRels, $name);
    //         }
    //     }
    // }
}
