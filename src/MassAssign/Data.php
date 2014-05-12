<?php

namespace CL\Luna\MassAssign;

use CL\Luna\Util\Arr;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Data extends UnsafeData
{
    public static function assignPermitted(array $data, array $permitted, AssignNodeInterface $node)
    {
        $data = new Data($data, $permitted);
        $data->assignTo($node);
    }

    protected $permitted;

    public function __construct(array $data, array $permitted)
    {
        parent::__construct($data);

        $this->permitted = Arr::toAssoc($permitted);
    }

    public function assignTo(AssignNodeInterface $node)
    {
        $data = array_intersect_key($this->data, $this->permitted);

        $node->setData($data, function (LinkSetDataInterface $link, array $data) {
            $name = $link->getRel()->getName();
            $permitted = isset($this->permitted[$name]) ? $this->permitted[$name] : array();

            $link->setData($data, function (AssignNodeInterface $node, array $data) use ($permitted) {
                Data::assignPermitted($data, $permitted, $node);
            });
        });
    }
}
