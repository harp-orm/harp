<?php namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Luna\Mapper\Repo;

/**
 * @group integration
 */
class FieldsTest extends AbstractTestCase {

    public function testTest()
    {
        Log::setEnabled(TRUE);

        $user = User::get(1);

        $user->delete();

        Repo::get()->persist($user);

        var_dump(Log::all());
    }
}
