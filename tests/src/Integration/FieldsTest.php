<?php

namespace CL\Luna\Test;

use CL\Luna\Test\Repo;

/**
 * @group integration
 */
class FieldsTest extends AbstractTestCase {

    public function testTest()
    {
        $user = Repo\User::get()->find(1);

        $user->delete();

        Repo\User::get()->save($user);

        var_dump($this->getLogger()->getEntries());
    }
}
