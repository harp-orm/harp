<?php

namespace Harp\Serializer;

use Closure;

/**
 * Add this trait to your object to make properties "serializable"
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait RelsTrait
{
    /**
     * @var Serializers
     */
    private $rels = [];

    /**
     * Get Serializers
     *
     * @return Serializers
     */
    public function getRels()
    {
        return $this->rels;
    }

    /**
     * @return self
     */
    public function addRel(AbstractRel $rel)
    {
        $this->rels[$rel->getName()] = $rel;

        return $this;
    }

    /**
     * @param  string $name
     * @return self
     */
    public function getRel($name)
    {
        if (false === $this->hasRel()) {
            throw new InvalidArgumentException(
                sprintf('Relationship %s does not exist on %s', $name, get_class($this))
            );
        }

        return $this;
    }

    public function hasRel($name)
    {
        return isset($this->rels[$name]);
    }
}
