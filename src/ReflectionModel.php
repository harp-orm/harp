<?php

namespace Harp\Harp;

use ReflectionClass;
use ReflectionProperty;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ReflectionModel extends ReflectionClass
{
    public function __construct($class)
    {
        parent::__construct($class);

        if (false === $this->isSubclassOf(__NAMESPACE__.'\Model')) {
            throw new InvalidArgumentException('Class must be sublcass of "Harp\Harp\Model"');
        }
    }

    public function hasTrait($trait)
    {
        return in_array($trait, $this->getTraitNames());
    }

    public function hasInheritedTrait()
    {
        return $this->hasTrait(__NAMESPACE__.'\Model\InheritedTrait');
    }

    public function hasSoftDeleteTrait()
    {
        return $this->hasTrait(__NAMESPACE__.'\Model\SoftDeleteTrait');
    }

    public function getPublicPropertyNames()
    {
        $publicProperties = $this->getProperties(ReflectionProperty::IS_PUBLIC);

        return array_map(function (ReflectionProperty $property) {
            return $property->getName();
        }, $publicProperties);
    }

}
