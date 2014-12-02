<?php

namespace Harp\Harp;

use Harp\Harp\Model\DirtyTrackingTrait;
use Harp\Harp\Model\UnmappedPropertiesTrait;
use Harp\IdentityMap\IdentityMapItemInterface;
use Harp\Validate\ValidateTrait;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class Model
{
    use DirtyTrackingTrait;
    use UnmappedPropertiesTrait;
    use ValidateTrait;
    use SessionLinkTrait;

    private $isVoid;

    /**
     * Set properties / state, unserialize properties and set original properties.
     *
     * @param array $properties
     * @param int   $state
     */
    public function __construct(array $properties = null, $isVoid = false)
    {
        $this->isVoid = $isVoid;

        if (false === empty($properties)) {
            $this->setProperties($properties);
        }

        $this->resetOriginals();
    }

    public function getId()
    {
        return $this->{Config::PRIMARY_KEY};
    }

    public function isVoid()
    {
        return $this->isVoid;
    }

    public function getConfig()
    {
        return $this->getSession()->getConfig(get_called_class())
    }

    public function getValidationAsserts()
    {
        return $this->getConfig()->getAsserts();
    }

    public function getRelModels($rel)
    {
        $rel = $this->getRel($rel);
        $loader = $rel->getModelLoader();

        return new RelOne($loader);
    }
}
