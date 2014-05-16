<?php

namespace CL\Luna\MassAssign;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Repo\LinkOne;
use CL\LunaCore\Repo\LinkMany;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class AssignModel
{
    private $model;

    public function __construct(AbstractModel $model)
    {
        $this->model = $model;
    }

    public function execute(UnsafeData $data)
    {
        $properties = $data->getPropertiesData($this->model);
        $this->model->setProperties($properties);

        $relsData = $data->getRelData($this->model);

        foreach ($relsData as $relName => $relData) {
            $link = $this->model->getRepo()->loadLink($this->model, $relName);

            if ($link instanceof LinkOne) {
                $assign = new AssignLinkOne($link);
            } elseif ($link instanceof LinkMany) {
                $assign = new AssignLinkMany($link);
            }

            $assign->execute($relData);
        }
    }
}
