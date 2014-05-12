<?php

namespace CL\Luna\MassAssign;

use CL\Luna\Mapper\LinkMany;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class AssignLinkMany extends AbstractAssignLink
{
    private $link;

    public function __construct(LinkMany $link)
    {
        $this->link = $link;
    }

    public function execute(UnsafeData $data)
    {
        $this->link->clear();

        foreach ($data->getArray() as $itemData) {
            $node = $this->loadNodeFromData($this->link->getRel(), $data);
            $node = $node ?: $this->link->getRel()->getForeignRepo()->newInstance();

            $this->link->add($node);

            $assign = new AssignNode($node);
            $assign->execute($itemData);
        }
    }
}
