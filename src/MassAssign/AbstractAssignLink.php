<?php

namespace CL\Luna\MassAssign;

use CL\LunaCore\Rel\AbstractRel;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractAssignLink
{
    public function loadNodeFromData(AbstractRel $rel, UnsafeData $data)
    {
        $data = $data->all();

        if (isset($data['_id'])) {
            if (isset($data['_repo'])) {
                $repoClass = $data['_repo'];

                $repo = $repoClass::get();
            } else {
                $repo = $rel->getForeignRepo();
            }

            return $repo->find($data['_id']);
        }
    }
}
