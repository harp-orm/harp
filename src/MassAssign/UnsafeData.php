<?php namespace CL\Luna\MassAssign;

use CL\Luna\Model\Model;
use CL\Luna\Mapper\LinkOne;
use CL\Luna\Mapper\Repo;
use CL\Luna\Mapper\AbstractRel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class UnsafeData
{
    public static function assign(array $data, AssignNodeInterface $model)
    {
        $data = new UnsafeData($data);
        $data->assignTo($model);

        return $model;
    }

    protected $data;

    public function getData()
    {
        return $this->data;
    }

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function assignTo(AssignNodeInterface $node)
    {
        $node->setData($this->data, function (LinkSetDataInterface $link, array $data) {
            $link->setData($data, function (AssignNodeInterface $node, array $data) {
                self::assign($data, $node);
            });
        });
    }
}
