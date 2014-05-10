<?php

namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Luna\Mapper\Repo;
use CL\Luna\Test\Store\UserStore;

/**
 * @group integration
 */
class FieldsTest extends AbstractTestCase {

    public function testTest()
    {
        Log::setEnabled(TRUE);

        $user = UserStore::get()->find(1);

        $user->delete();

        Repo::get()->persist($user);

        var_dump(Log::all());
    }
}
