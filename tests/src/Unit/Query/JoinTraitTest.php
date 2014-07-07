<?php

namespace Harp\Harp\Test\Unit\Query;

use Harp\Harp\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Query\JoinRelTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class JoinRelTest extends AbstractTestCase
{
    /**
     * @covers ::joinRels
     * @covers ::joinNestedRels
     */
    public function testJoinRels()
    {
        $repo = Model\User::getRepo();

        $select = new Select($repo);

        $select->joinRels(['address', 'posts' => 'tags']);

        $this->assertEquals(
            'SELECT `User`.* FROM `User` JOIN `Address` AS `address` ON `address`.`id` = `User`.`addressId` JOIN `Post` AS `posts` ON `posts`.`userId` = `User`.`id` JOIN `PostTag` AS `postTags` ON `postTags`.`postId` = `posts`.`id` JOIN `Tag` AS `tags` ON `tags`.`id` = `postTags`.`tagId`',
            $select->humanize()
        );
    }

    /**
     * @covers ::joinNestedRels
     * @expectedException InvalidArgumentException
     */
    public function testJoinRelsError()
    {
        $repo = Model\User::getRepo();

        $select = new Select($repo);

        $select->joinRels(['unknown address']);
    }
}
