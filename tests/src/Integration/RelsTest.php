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

        $user = Post::all()->joinRels(['user' => 'address']);

        $user->load();

        var_dump(Log::all());
    }
}
