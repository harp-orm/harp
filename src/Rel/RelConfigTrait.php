<?php

namespace Harp\Harp\Rel;

use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use InvalidArgumentException;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
trait RelConfigTrait
{
    private $rels;

    /**
     * @return AbstractRel[]
     */
    public function getRels()
    {
        return $this->rels;
    }

    /**
     * @param  string           $name
     * @return AbstractRel|null
     */
    public function getRel($name)
    {
        return isset($this->rels[$name]) ? $this->rels[$name] : null;
    }

    /**
     * @param  string                   $name
     * @return AbstractRel
     * @throws InvalidArgumentException If rel does not exist
     */
    public function getRelOrError($name)
    {
        $rel = $this->getRel($name);

        if ($rel === null) {
            throw new InvalidArgumentException(sprintf('Rel %s does not exist', $name));
        }

        return $rel;
    }

    /**
     * @return self
     */
    public function addRel(AbstractRel $rel)
    {
        $this->rels[$rel->getName()] = $rel;

        return $this;
    }

    public function belongsTo($name, $foreignModel, array $parameters = array())
    {
        $this->addRel(new BelongsTo($name, $this, $foreignModel, $parameters));

        return $this;
    }

    public function belongsToPolymorphic($name, $foreignModel, array $parameters = array())
    {
        $this->addRel(new BelongsToPolymorphic($name, $this, $foreignModel, $parameters));

        return $this;
    }

    public function hasMany($name, $foreignModel, array $parameters = array())
    {
        $this->addRel(new HasMany($name, $this, $foreignModel, $parameters));

        return $this;
    }

    public function hasManyAs($name, $foreignModel, $foreignKeyName, array $parameters = array())
    {
        $this->addRel(new HasManyAs($name, $this, $foreignModel, $foreignKeyName, $parameters));

        return $this;
    }

    public function hasManyExclusive($name, $foreignModel, array $parameters = array())
    {
        $this->addRel(new HasManyExclusive($name, $this, $foreignModel, $parameters));

        return $this;
    }

    public function hasManyThrough($name, $foreignModel, $through, array $parameters = array())
    {
        $this->addRel(new HasManyThrough($name, $this, $foreignModel, $through, $parameters));

        return $this;
    }

    public function hasOne($name, $foreignModel, array $parameters = array())
    {
        $this->addRel(new HasOne($name, $this, $foreignModel, $parameters));

        return $this;
    }
}
