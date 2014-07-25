<?php

namespace Harp\Harp\Test\Integration;

use Harp\Harp\Test\AbstractDbTestCase;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Profile;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\Address;
use Harp\Query\SQL\SQL;

/**
 * @group integration
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LoadingTest extends AbstractDbTestCase
{
    /**
     * @coversNothing
     */
    public function testLoadVoid()
    {
        $user = new User();

        $profile = $user->getProfile();

        $this->assertInstanceof('Harp\Harp\Test\TestModel\Profile', $profile);
        $this->assertTrue($profile->isVoid());

        $profile = new Profile();

        $user = $profile->getUser();

        $this->assertInstanceof('Harp\Harp\Test\TestModel\User', $user);
        $this->assertTrue($user->isVoid());
    }

    /**
     * @coversNothing
     */
    public function testFind()
    {
        $user = User::find(1);

        $address = Address::find(1);

        $this->assertInstanceOf('Harp\Harp\Test\TestModel\User', $user);
        $this->assertInstanceOf('Harp\Harp\Test\TestModel\Address', $address);

        $obj = new SaveableObject();
        $obj->setVar('test1');

        $userProps = [
            'id' => 1,
            'name' => 'User 1',
            'password' => null,
            'addressId' => 1,
            'isBlocked' => 0,
            'deletedAt' => null,
            'locationId' => 1,
            'locationClass' => 'Harp\\Harp\\Test\\TestModel\\City',
            'test' => null,
            'object' => $obj,
            'parentId' => null,
        ];

        $addressProps = [
            'id' => 1,
            'zipCode' => '1000',
            'location' => 'Belvedere',
        ];

        $this->assertEquals($userProps, $user->getProperties());
        $this->assertEquals($addressProps, $address->getProperties());

        $this->assertQueries([
            'SELECT `User`.* FROM `User` WHERE (`id` = 1) AND (`User`.`deletedAt` IS NULL) LIMIT 1',
            'SELECT `Address`.* FROM `Address` WHERE (`id` = 1) LIMIT 1'
        ]);
    }

    /**
     * @coversNothing
     */
    public function testFindAll()
    {
        $cities = City::where('countryId', 1)->load();

        $this->assertInstanceOf('Harp\Harp\Model\Models', $cities);
        $this->assertCount(2, $cities);

        $cities->rewind();

        $this->assertSame(
            [
                'id' => 1,
                'name' => 'Sofia',
                'countryId' => 1,
            ],
            $cities->current()->getProperties()
        );

        $cities->next();

        $this->assertSame(
            [
                'id' => 2,
                'name' => 'Pernik',
                'countryId' => 1,
            ],
            $cities->current()->getProperties()
        );

        $this->assertQueries([
            'SELECT `City`.* FROM `City` WHERE (`countryId` = 1)',
        ]);
    }

    /**
     * @coversNothing
     */
    public function testComplexFindAll()
    {
        $users = User::findAll()
            ->where('name', 'User 1')
            ->whereNot('addressId', null)
            ->limit(2)
            ->order('isBlocked', 'DESC')
            ->joinAliased('Address', 'adr', ['addressId' => 'adr.id'])
            ->having('password', null)
            ->group('User.id')
            ->column(new SQL('CONCAT(name, "test")'), 'param')
            ->prependColumn(new SQL('COUNT(addressId)'), 'param2')
            ->load();

        $user = $users->getFirst();

        $obj = new SaveableObject();
        $obj->setVar('test1');

        $userProps = [
            'id' => 1,
            'name' => 'User 1',
            'password' => null,
            'addressId' => 1,
            'isBlocked' => 0,
            'deletedAt' => null,
            'locationId' => 1,
            'locationClass' => 'Harp\\Harp\\Test\\TestModel\\City',
            'test' => null,
            'object' => $obj,
            'parentId' => null,
        ];

        $this->assertEquals($userProps, $user->getProperties());

        $userUnmapped = [
            'param2' => 1,
            'param' => 'User 1test',
        ];

        $this->assertSame($userUnmapped, $user->getUnmapped());

        $this->assertQueries([
            'SELECT COUNT(addressId) AS `param2`, `User`.*, CONCAT(name, "test") AS `param` FROM `User` JOIN `Address` AS `adr` ON `addressId` = `adr`.`id` WHERE (`name` = "User 1") AND (`addressId` IS NOT NULL) AND (`User`.`deletedAt` IS NULL) GROUP BY `User`.`id` HAVING (`password` IS NULL) ORDER BY `isBlocked` DESC LIMIT 2',
        ]);
    }

    /**
     * @coversNothing
     */
    public function testJoinRels()
    {
        $users = User::findAll()
            ->joinRels(['posts' => 'tags'])
            ->group('User.id')
            ->load();

        $expected = User::find(1);

        $this->assertSame($expected, $users->getFirst());

        $addresses = Address::findAll()
            ->joinRels(['user' => 'posts'])
            ->load();

        $expected = Address::find(1);

        $this->assertSame($expected, $addresses->getFirst());

        $this->assertQueries([
            'SELECT `User`.* FROM `User` JOIN `Post` AS `posts` ON `posts`.`userId` = `User`.`id` JOIN `PostTag` AS `postTags` ON `postTags`.`postId` = `posts`.`id` JOIN `Tag` AS `tags` ON `tags`.`id` = `postTags`.`tagId` WHERE (`User`.`deletedAt` IS NULL) GROUP BY `User`.`id`',
            'SELECT `User`.* FROM `User` WHERE (`id` = 1) AND (`User`.`deletedAt` IS NULL) LIMIT 1',
            'SELECT `Address`.* FROM `Address` JOIN `User` AS `user` ON `user`.`addressId` = `Address`.`id` AND `user`.`deletedAt` IS NULL JOIN `Post` AS `posts` ON `posts`.`userId` = `user`.`id`',
            'SELECT `Address`.* FROM `Address` WHERE (`id` = 1) LIMIT 1',
        ]);
    }

    /**
     * @coversNothing
     */
    public function testLoadIds()
    {
        $ids = City::where('countryId', 2)->loadIds();

        $expected = [3, 4];

        $this->assertSame($expected, $ids);

        $this->assertQueries([
            'SELECT `City`.`id` AS `id` FROM `City` WHERE (`countryId` = 2)',
        ]);
    }

    /**
     * @coversNothing
     */
    public function testLoadCount()
    {
        $count = City::where('countryId', 2)->loadCount();

        $this->assertSame(2, $count);

        $this->assertQueries([
            'SELECT COUNT(`City`.`id`) AS `countAll` FROM `City` WHERE (`countryId` = 2)',
        ]);
    }

    /**
     * @coversNothing
     */
    public function testLoadWith()
    {
        $users = User::findAll()
            ->loadWith(['address', 'posts' => 'tags']);

        $this->assertCount(4, $users);

        $user = $users->getFirst();

        $this->assertEquals(1, $user->id);
        $this->assertEquals(1, $user->getAddress()->id);
        $this->assertCount(1, $user->getPosts());
        $this->assertEquals(1, $user->getPosts()->getFirst()->id);
        $this->assertCount(2, $user->getPosts()->getFirst()->getTags());
        $this->assertEquals([1, 2], $user->getPosts()->getFirst()->getTags()->get()->getIds());

        $user = $users->getNext();

        $this->assertEquals(2, $user->id);
        $this->assertTrue($user->getAddress()->isVoid());
        $this->assertCount(0, $user->getPosts());

        $user = $users->getNext();

        $this->assertEquals(3, $user->id);
        $this->assertTrue($user->getAddress()->isVoid());
        $this->assertCount(1, $user->getPosts());

        $this->assertInstanceof('Harp\Harp\Test\TestModel\BlogPost', $user->getPosts()->getFirst());
        $this->assertEquals(4, $user->getPosts()->getFirst()->id);
        $this->assertCount(0, $user->getPosts()->getFirst()->getTags());

        $user = $users->getNext();

        $this->assertEquals(4, $user->id);
        $this->assertEquals(2, $user->getAddress()->id);

        $posts = $user->getPosts();

        $this->assertCount(2, $posts);

        $post = $posts->getFirst();

        $this->assertEquals(2, $post->id);
        $this->assertCount(0, $post->getTags());

        $post = $posts->getNext();

        $this->assertEquals(3, $post->id);
        $this->assertCount(1, $post->getTags());
        $this->assertEquals([2], $post->getTags()->get()->getIds());

        $this->assertQueries([
            'SELECT `User`.* FROM `User` WHERE (`User`.`deletedAt` IS NULL)',
            'SELECT `Address`.* FROM `Address` WHERE (`id` IN (1, 2))',
            'SELECT `Post`.`class`, `Post`.* FROM `Post` WHERE (`userId` IN (1, 2, 3, 4))',
            'SELECT `Tag`.*, `postTags`.`postId` AS `tagsKey` FROM `Tag` JOIN `PostTag` AS `postTags` ON `postTags`.`tagId` = `Tag`.`id` WHERE (`postTags`.`postId` IN (1, 2, 3, 4))',
        ]);
    }
}
