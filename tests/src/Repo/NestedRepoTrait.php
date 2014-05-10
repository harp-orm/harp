<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\Model\Repo;
use CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait NestedRepoTrait {

    public static function initializeTrait(Repo $store)
    {
        $store
            ->getFields()
                ->add(new Field\Integer('parentId'));
    }
}