<?php

namespace CL\Luna\MassAssign;

use CL\LunaCore\Model\AbstractModel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class UnsafeData
{
    protected $data;

    public function all()
    {
        return $this->data;
    }

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function assignTo(AbstractModel $node)
    {
        $assign = new AssignModel($node);
        $assign->execute($this);

        return $this;
    }

    public function getPropertiesData(AbstractModel $node)
    {
        $rels = $node->getRepo()->getRels()->all();

        return array_diff_key($this->data, $rels);
    }

    public function getRelData(AbstractModel $node)
    {
        $rels = $node->getRepo()->getRels()->all();

        $relData = array_intersect_key($this->data, $rels);

        foreach ($relData as & $data) {
            $data = new UnsafeData($data);
        }

        return $relData;
    }

    public function getArray()
    {
        return array_map(function ($data) {
            return new UnsafeData($data);
        }, $this->data);
    }
}
