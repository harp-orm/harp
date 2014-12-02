<?php

namespace Harp\Harp;

/**
 * A dependancy injection container for Repo objects
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ConfigContainer
{
    use SessionLinkTrait;

    private $classes;

    private $aliases;


    public function __construct(Session $session, array $aliases = array())
    {
        $this->aliases = $aliases;
        $this->setSession($session);
    }

    public function get($class)
    {
        $class = $this->getAliasedClass($class);

        if (false === $this->has($class)) {
            $this->classes[$class] = new Config($this->getSession(), $class);
        }

        return $this->classes[$class];
    }

    public function has($class)
    {
        return isset($this->configs[$class]);
    }

    public function getAliasedClass($class)
    {
        return isset($this->aliases[$class]
            ? $this->aliases[$class]
            : $class;
    }
}
