<?php

namespace CL\Luna\MassAssign;

use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Mapper\LinkOne;
use CL\Luna\Mapper\LinkMany;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class AssignNode
{
    private $node;

    public function __construct(AbstractNode $node)
    {
        $this->node = $node;
    }

    public function execute(UnsafeData $data)
    {
        $properties = $data->getPropertiesData($this->node);
        $this->node->setProperties($properties);

        $relsData = $data->getRelData($this->node);

        foreach ($relsData as $relName => $relData) {
            $link = $this->node->getRepo()->loadLink($this->node, $relName);

            if ($link instanceof LinkOne) {
                $assign = new AssignLinkOne($link);
            } elseif ($link instanceof LinkMany) {
                $assign = new AssignLinkMany($link);
            }

            $assign->execute($relData);
        }
    }
}
