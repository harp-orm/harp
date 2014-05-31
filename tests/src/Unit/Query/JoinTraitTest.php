<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Select;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\JoinRelTrait
 */
class JoinRelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::joinRels
     * @covers ::joinNestedRels
     */
    public function testJoinRels()
    {
        $repo = new Repo\User('Harp\Harp\Test\Model\User');

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
        $repo = new Repo\User('Harp\Harp\Test\Model\User');

        $select = new Select($repo);

        $select->joinRels(['unknown address']);
    }
}
