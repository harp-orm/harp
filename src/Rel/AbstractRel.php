<?php

namespace Harp\Harp\Rel;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsTo
{
    abstract function areLinked(Model $model, Model $foreign);
    abstract function join(Select $select);
    abstract function change();

    private $foreign;

    public function __construct(Session $session, $name, $foreign)
    {
        $this->setSession($session);
        $this->name = $name;
        $this->foreign = $foreign;
    }

    public function getForeign()
    {
        return $this->getSession()->getConfig($this->foreign);
    }
}
