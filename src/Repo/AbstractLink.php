<?php

namespace Harp\Harp\Repo;

use Harp\Harp\Rel\AbstractRel;
use Harp\Harp\Rel\UpdateInverseInterface;
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;

/**
 * A basic "link" between models. Links are the concrete instance of a relation between models.
 * A link will hold a reference of all the models invovled (parent and foreign) as well as
 * a reference to the rel itself.
 *
 * It will also hold historical data (e.g. getOriginal).
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractLink
{
    /**
     * @var AbstractRel
     */
    private $rel;

    /**
     * @var AbstractModel
     */
    private $model;

    /**
     * @param AbstractModel $model
     * @param AbstractRel   $rel
     */
    public function __construct(AbstractModel $model, AbstractRel $rel)
    {
        $this->rel = $rel;
        $this->model = $model;
    }

    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Call updateInverse on the rel
     *
     * @param  AbstractModel $foreign
     */
    public function updateInverse(AbstractModel $foreign)
    {
        $rel = $this->getRel()->getInverseOfRel();

        if ($rel instanceof UpdateInverseInterface) {
            $rel->updateInverse($this->getModel(), $foreign);
        }
    }

    /**
     * @return AbstractModel
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Models|null
     */
    abstract public function delete();

    /**
     * @return Models|null
     */
    abstract public function insert();

    /**
     * @return Models|null
     */
    abstract public function update();

    /**
     * @return Models
     */
    abstract public function getCurrentAndOriginal();

    /**
     * @return boolean
     */
    abstract public function isChanged();

    abstract public function clear();

    abstract public function get();

    abstract public function getOriginal();
}
