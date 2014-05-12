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

    public function getPropertiesData(AbstractNode $node)
    {
        $rels = $node->getRepo()->getRels()->all();

        $data = array_intersect_key($this->data, $this->permitted);

        return array_diff_key($data, $rels);
    }

    public function getRelData(AbstractNode $node)
    {
        $rels = $node->getRepo()->getRels()->all();

        $relData = array_intersect_key($this->data, $rels);

        foreach ($relData as $relName => & $data) {
            $permitted = isset($this->permitted[$relName]) ? $this->permitted[$relName] : [];
            $data = new Data($data, $permitted);
        }

        return $relData;
    }

    public function getArray()
    {
        return array_map(function ($data) {
            return new Data($data, $this->permitted);
        }, $this->data);
    }
}
