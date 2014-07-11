<?php

namespace Harp\Harp\Model;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class State
{
    const PENDING = 1;
    const DELETED = 2;
    const SAVED = 4;
    const VOID = 8;
}
