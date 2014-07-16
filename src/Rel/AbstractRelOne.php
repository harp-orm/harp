<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Repo\LinkOne;

/**
 * Represents linking of one model to another model. A basis a "belongs to" association.
 * A "one" relation will always return a LinkOne result with a model. If a model cannot be loaded,
 * a "void model will be created for the foreign repo.
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractRelOne extends AbstractRel implements UpdateInverseInterface
{
    /**
     * Return a LinkOne based on the linked model. Only the first linked model is considered,
     * generally this array should conain only one model anyway.
     * (its only an array for consistency with RelMany).
     *
     * If no model is found, will the link will hold a void model from the foreign repo.
     *
     * @param  AbstractModel $model
     * @param  array         $linked
     * @return LinkOne
     */
    public function newLinkFrom(AbstractModel $model, array $linked)
    {
        if (empty($linked)) {
            $foreign = $this->getRepo()->newVoidModel();
        } else {
            $foreign = reset($linked);
            $foreign = $foreign->getRepo()->getIdentityMap()->get($foreign);
        }

        return new LinkOne($model, $this, $foreign);
    }

    /**
     * @param  AbstractModel $model
     * @param  AbstractModel $foreign
     */
    public function updateInverse(AbstractModel $model, AbstractModel $foreign)
    {
        $link = $foreign->getLinkOne($this->getName());

        if ($link->get() !== $model) {
            $link->set($model);
        }
    }
}
