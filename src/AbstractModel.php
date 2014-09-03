<?php

namespace Harp\Harp;

use Harp\Harp\Model\StateTrait;
use Harp\Harp\Model\DirtyTrackingTrait;
use Harp\Harp\Model\UnmappedPropertiesTrait;
use Harp\IdentityMap\IdentityMapItemInterface;
use Harp\Harp\Model\RepoTrait;
use Harp\Validate\ValidateTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
abstract class AbstractModel implements IdentityMapItemInterface
{
    use StateTrait;
    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;
    use RepoTrait;
    use ValidateTrait;

    /**
     * Set properties / state, unserialize properties and set original properties.
     *
     * @param array $properties
     * @param int   $state
     */
    public function __construct(array $properties = null, $state = null)
    {
        $this->setState($state ?: $this->getDefaultState());

        if (! empty($properties)) {
            $this->setProperties($properties);
        }

        self::getRepo()->initializeModel($this);

        $this->resetOriginals();
    }

    public function hasSavedProperties()
    {
        return (bool) $this->getId();
    }

    public function getValidationAsserts()
    {
        return self::getRepo()->getAsserts();
    }

    /**
     * This method will be overridden by SoftDeleteTrait
     *
     * @return boolean
     */
    public function isSoftDeleted()
    {
        return false;
    }

    public function getIdentityKey()
    {
        return $this->isSaved() ? $this->getId() : null;
    }
}
