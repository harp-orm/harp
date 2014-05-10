<?php

namespace CL\Luna\Test;

use CL\Luna\Model\Store;
use CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait NestedStoreTrait {

    public static function initializeTrait(Store $Store)
    {
        $Store
            ->getFields()
                ->add(new Field\Integer('parentId'));
    }
}
