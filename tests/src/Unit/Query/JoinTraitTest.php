<?php

namespace CL\Luna\Test\Unit\Query;

use CL\Luna\Test\Repo;
use CL\Luna\Test\Model;
use CL\LunaCore\Model\Models;
use CL\Atlas\SQL;
use CL\Luna\Query\Select;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass CL\Luna\Query\JoinRelTrait
 */
class JoinRelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::joinRels
     * @covers ::joinNestedRels
     */
    public function testJoinRels()
    {
        $repo = new Repo\User('CL\Luna\Test\Model\User');

        $select = new Select($repo);

        $select->joinRels(['address', 'posts' => 'tags']);

        $this->assertEquals(
            'SELECT User.* FROM User JOIN Address AS address ON address.id = User.addressId JOIN Post AS posts ON posts.userId = User.id JOIN PostTag AS postTags ON postTags.postId = posts.id JOIN Tag AS tags ON tags.id = postTags.tagId',
            $select->humanize()
        );
    }

    /**
     * @covers ::joinNestedRels
     * @expectedException InvalidArgumentException
     */
    public function testJoinRelsError()
    {
        $repo = new Repo\User('CL\Luna\Test\Model\User');

        $select = new Select($repo);

        $select->joinRels(['unknown address']);
    }
}
