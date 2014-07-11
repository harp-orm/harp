<?php

namespace Harp\Harp\Repo;

use Harp\Util\Arr;
use ReflectionClass;
use ReflectionProperty;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ReflectionModel extends ReflectionClass
{
    /**
     * @return array
     */
    public function getPublicPropertyNames()
    {
        $properties = $this->getProperties(ReflectionProperty::IS_PUBLIC);

        return Arr::invoke($properties, 'getName');
    }

    public function initialize($subject)
    {
        if ($this->hasMethod('initialize')) {
            $this->getMethod('initialize')->invoke(null, $subject);
        }
    }

    /**
     * @return boolean
     */
    public function isRoot()
    {
        return $this->getParentClass()->getName() === 'Harp\Harp\AbstractModel';
    }

    /**
     * @return ReflectionClass
     */
    public function getRoot()
    {
        $class = $this;

        while ($class->getParentClass()->getName() !== 'Harp\Harp\AbstractModel') {
            $class = $class->getParentClass();
        }

        return $class;
    }
}
