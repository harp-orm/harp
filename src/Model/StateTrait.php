<?php

namespace Harp\Harp\Model;

use LogicException;

/**
 * Add class property and methods to work with inheritence.
 * You need to call setInherited(true) on the corresponding repo
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait StateTrait
{
    /**
     * Implement this to check if the model has all the properties to be considered "SAVED"
     *
     * @return boolean
     */
    abstract public function hasSavedProperties();

    /**
     * @var int
     */
    private $state;

    /**
     * Default state of models with "id" is State::SAVED, otherwise - State::PENDING
     *
     * @return int
     */
    public function getDefaultState()
    {
        return $this->hasSavedProperties() ? State::SAVED : State::PENDING;
    }

    /**
     * if the model has id, it becomes SAVED, otherwise - pending
     *
     * @return AbstractModel $this
     */
    public function setStateNotVoid()
    {
        if ($this->state === State::VOID) {
            $this->state = $this->hasSavedProperties() ? State::SAVED : State::PENDING;
        }

        return $this;
    }

    /**
     * Void models will not be saved.
     *
     * @return AbstractModel $this
     */
    public function setStateVoid()
    {
        $this->state = State::VOID;

        return $this;
    }

    /**
     * @param  int           $state
     * @return AbstractModel $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return int ModelState
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return boolean
     */
    public function isSaved()
    {
        return $this->state === State::SAVED;
    }

    /**
     * @return boolean
     */
    public function isPending()
    {
        return $this->state === State::PENDING;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->state === State::DELETED;
    }

    /**
     * @return boolean
     */
    public function isVoid()
    {
        return $this->state === State::VOID;
    }

    /**
     * Set state as deleted (You need to save it to delete it from the repo)
     *
     * @return AbstractModel $this
     */
    public function delete()
    {
        if ($this->state === State::PENDING) {
            throw new LogicException('You cannot delete pending models');
        } elseif ($this->state === State::SAVED) {
            $this->state = State::DELETED;
        }

        return $this;
    }

}
