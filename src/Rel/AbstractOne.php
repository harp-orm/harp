<?php namespace CL\Luna\Rel;

use CL\Luna\Repo\LinkOne;
use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractOne extends AbstractRel
{
    // abstract public function update(Model $model, LinkOne $link);
    // abstract public function unlinkModel(Model $model);

    // public function cascadeDelete(Model $model, LinkOne $link)
    // {
    //     if ($this->getCascade() === AbstractRel::UNLINK) {
    //         $this->unlinkModels($link->all());
    //     } elseif ($this->getCascade() === AbstractRel::DELETE) {
    //         $this->deleteModels($link->all());
    //     }
    // }
}
