<?php

namespace Harp\Harp;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
interface LoaderInterface
{
    public function getModels();
    public function getSelect();
    public function getVoidModel();
}
