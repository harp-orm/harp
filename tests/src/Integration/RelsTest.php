<?php

namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Luna\Mapper\Repo;
use CL\Luna\Test\Store\UserStore;

/**
 * @group integration
 */
class RelsTest extends AbstractTestCase {

    public function testTest()
    {
        $users = UserStore::get()->findAll()->loadWith(['profile', 'posts']);

        var_dump($this->getLogger()->getEntries());
    }
}
