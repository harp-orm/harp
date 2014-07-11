<?php

namespace Harp\Harp\Test;

use Harp\Harp\AbstractModel;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Model\Models;
use Harp\Query\AbstractWhere;
use Harp\Harp\Rel\AbstractRelOne;
use Harp\Harp\Rel\DeleteOneInterface;
use Harp\Harp\Rel\InsertOneInterface;
use Harp\Harp\Rel\UpdateOneInterface;
use LogicException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class TestBelongsTo extends AbstractRelOne implements DeleteOneInterface, InsertOneInterface, UpdateOneInterface
{
    public function delete(LinkOne $link)
    {
        throw new LogicException('this should not be called');
    }

    public function insert(LinkOne $link)
    {
        throw new LogicException('this should not be called');
    }

    public function update(LinkOne $link)
    {
        throw new LogicException('this should not be called');
    }

    public function areLinked(AbstractModel $model, AbstractModel $foreignModel)
    {
        throw new LogicException('this should not be called');
    }

    public function hasModels(Models $models)
    {
        throw new LogicException('this should not be called');
    }

    public function loadModels(Models $models, $flags = null)
    {
        throw new LogicException('this should not be called');
    }

    public function join(AbstractWhere $query, $parent)
    {
        throw new LogicException('this should not be called');
    }

}
