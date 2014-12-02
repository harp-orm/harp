<?php

namespace Harp\Harp\Rel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsTo extends AbstractRel
{
    public function getSelect(Session $session, Model $model)
    {
        $config = $session->getConfig($this->getForeignModel());

        $foreignKey = $config->getTable().'.'.$config->getPrimaryKey();
        $key = $this->getKey();

        return $session
            ->getSelect($this->getForeignModel())
                ->where($field, $model->id());
    }

    public function areLinked(Model $model, Model $foreign)
    {
        return $model->
    }

    public function join(Select $select)
    {

    }

    public function change(Session $session, LinkOne $linkOne)
    {
        # code...
    }
}
