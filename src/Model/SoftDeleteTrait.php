<?php

namespace Harp\Harp\Model;

use Harp\Harp\Config;

/**
 * Add deletedAt property and methods to work with soft deletion.
 * Also overrides several getDefaultState, delete, isSoftDeleted to return appropriate values,
 * if the model is "soft deleted"
 * You need to call setSoftDelete(true) on the corresponding repo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait SoftDeleteTrait
{
    public static function initialize(Config $repo)
    {
        $repo->setSoftDelete(true);
    }

    /**
     * @param int $state
     */
    abstract public function setState($state);

    /**
     * @return boolean
     */
    abstract public function isDeleted();

    /**
     * @var int
     */
    public $deletedAt;

    /**
     * @return SoftDeleteTrait $this
     */
    public function delete()
    {
        $this->deletedAt = time();

        parent::delete();

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultState()
    {
        return $this->deletedAt ? State::DELETED : parent::getDefaultState();
    }

    /**
     * @return SoftDeleteTrait $this
     */
    public function realDelete()
    {
        $this->deletedAt = null;

        parent::delete();

        return $this;
    }

    /**
     * @return SoftDeleteTrait $this
     */
    public function restore()
    {
        $this->deletedAt = null;
        $this->setState(State::SAVED);

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSoftDeleted()
    {
        return ($this->isDeleted() and $this->deletedAt !== null);
    }
}
