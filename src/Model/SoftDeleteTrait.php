<?php

namespace Harp\Harp\Model;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait SoftDeleteTrait
{
    /**
     * @var string
     */
    public $deletedAt;
}
