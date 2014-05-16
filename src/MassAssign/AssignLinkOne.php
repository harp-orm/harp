<?php

namespace CL\Luna\MassAssign;

use CL\LunaCore\Repo\LinkOne;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class AssignLinkOne extends AbstractAssignLink
{
    private $link;

    public function __construct(LinkOne $link)
    {
        $this->link = $link;
    }

    public function execute(UnsafeData $data)
    {
        $node = $this->loadNodeFromData($this->link->getRel(), $data) ?: $this->link->get();
        $this->link->set($node);

        $assign = new AssignModel($node);
        $assign->execute($data);
    }
}
