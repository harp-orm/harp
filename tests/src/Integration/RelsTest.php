<?php

namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Luna\Test\Repo;

/**
 * @group integration
 */
class RelsTest extends AbstractTestCase {

    public function testTest()
    {
        $users = Repo\User::get()->findAll()->loadWith(['profile', 'posts']);

        var_dump($this->getLogger()->getEntries());
    }
}
