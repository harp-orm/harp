<?php namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Luna\Mapper\Repo;

/**
 * @group integration
 */
class RelsTest extends AbstractTestCase {

    public function testTest()
    {
        Log::setEnabled(TRUE);

        $users = User::findAll()->eagerLoad(['profile', 'posts']);

        var_dump(Log::all());
    }
}
