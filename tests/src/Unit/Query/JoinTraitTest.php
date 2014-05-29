<?php

namespace Harp\Db\Test\Unit\Query;

use Harp\Db\Test\Repo;
use Harp\Db\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Db\Query\Select;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Db\Query\JoinRelTrait
 */
class JoinRelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::joinRels
     * @covers ::joinNestedRels
     */
    public function testJoinRels()
    {
        $repo = new Repo\User('Harp\Db\Test\Model\User');

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
        $repo = new Repo\User('Harp\Db\Test\Model\User');

        $select = new Select($repo);

        $select->joinRels(['unknown address']);
    }
}
