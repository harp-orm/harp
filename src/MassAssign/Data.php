<?php

namespace CL\Luna\MassAssign;

use CL\Luna\Util\Arr;
use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Mapper\AbstractLink;
use CL\Luna\Mapper\LinkOne;
use CL\Luna\Mapper\LinkMany;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Data extends UnsafeData
{
    protected $permitted;

    public function __construct(array $data, array $permitted)
    {
        parent::__construct($data);

        $this->permitted = Arr::toAssoc($permitted);
    }

    public function assignTo(AbstractNode $node)
    {
        $data = array_intersect_key($this->data, $this->permitted);

        $this->setDataNode($node, $this->data, function (AbstractLink $link, array $data) {

            $name = $link->getRel()->getName();
            $permitted = isset($this->permitted[$name]) ? $this->permitted[$name] : array();

            $assign = function(AbstractNode $node, array $data) use ($permitted) {
                $data = new Data($data, $permitted);
                $data->assignTo($node);
            };

            if ($link instanceof LinkOne) {
                $this->setDataLinkOne($link, $data, $assign);
            } elseif ($link instanceof LinkMany) {
                $this->setDataLinkMany($link, $data, $assign);
            }
        });
    }
}
