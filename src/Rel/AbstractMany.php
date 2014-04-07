<?php namespace CL\Luna\Rel;

use CL\Luna\Repo\LinkMany;
use CL\Luna\Model\Model;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractMany extends AbstractRel
{
    // abstract public function update(Model $model, LinkMany $link);
    // abstract public function unlinkModels(Model $model, LinkMany $link);

    // public function deleteModels(SplObjectStorage $models)
    // {
    //     foreach ($models as $model) {
    //         $model->delete();
    //     }
    // }

    // public function cascadeDelete(Model $model, LinkMany $link)
    // {
    //     if ($this->getCascade() === AbstractRel::UNLINK) {
    //         $this->unlinkModels($link->all());
    //     } elseif ($this->getCascade() === AbstractRel::DELETE) {
    //         $this->deleteModels($link->all());
    //     }
    // }
}
