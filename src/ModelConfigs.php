<?php

namespace Harp\Harp\Repo;

/**
 * A dependancy injection container for Repo objects
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ModelConfigs
{
    private static $configs;

    public function get($class)
    {
        if (false === isset($this->configs[$class])) {
            $this->configs[$class] = new Config($class);
        }

        return $this->configs[$class];
    }
}
