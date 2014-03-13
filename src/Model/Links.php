<?php namespace CL\Luna\Model;

use CL\Luna\Util\ObjectStorage;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Repo\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Links extends ObjectStorage
{
    public function getAllModels()
    {
        $items = new ModelsGroup();

        foreach ($this as $rel)
        {
            foreach ($this->getInfo()->getAll() as $model)
            {
                $items->add($model);
            }
        }

        return $items;
    }

    public function load(AbstractRel $rel, Model $parent)
    {
        Repo::getInstance()->loadLinkArray($rel, [$parent]);
        return $this;
    }

    public function update(Model $parent)
    {
        foreach ($this as $rel)
        {
            $rel->update($parent, $this->getInfo());
        }
        return $this;
    }
}
