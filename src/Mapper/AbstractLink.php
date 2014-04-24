<?php

namespace CL\Luna\Mapper;

use CL\Luna\MassAssign\LinkSetDataInterface;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractLink implements LinkSetDataInterface
{
    protected $rel;

    public function __construct(AbstractRel $rel)
    {
        $this->rel = $rel;
    }

    public function getRel()
    {
        return $this->rel;
    }

    abstract public function getAll();
}
