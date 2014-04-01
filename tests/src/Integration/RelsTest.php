<?php namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Luna\Repo\Repo;

/**
 * @group integration
 */
class RelsTest extends AbstractTestCase {

    public function testTest()
    {
        Log::setEnabled(TRUE);

        $users = User::all()->loadWith(['profile', 'posts']);

        var_dump($users[0]->getProfile());
        var_dump($users[3]->getProfile());

        var_dump(Log::all());
    }
}
