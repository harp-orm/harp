<?php

namespace Harp\Harp\Test\Integration;

use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\Repo;
use Harp\Harp\Test\Model;
use Harp\Core\Repo\Container;
use Harp\Query\SQL\SQL;
use stdClass;

/**
 * @group integration
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class SavingTest extends AbstractTestCase {

    /**
     * @coversNothing
     */
    public function testBasic()
    {
        $user = Model\User::find(1);
        $user->name = 'New Name';
        $user->isBlocked = true;

        Model\User::save($user);

        $this->assertQueries([
            'SELECT `User`.* FROM `User` WHERE (`id` = 1) AND (`User`.`deletedAt` IS NULL) LIMIT 1',
            'UPDATE `User` SET `name` = "New Name", `isBlocked` = 1 WHERE (`id` = 1)',
        ]);
    }

    /**
     * @coversNothing
     */
    public function testRels()
    {
        $user = Model\User::find(1);
        $user->name = 'New Name';
        $user->isBlocked = true;
        $user->object = new SaveableObject();
        $user->object->setVar('value');

        $address = $user->getAddress();
        $address->location = 'Somewhere else';
        $address->zipCode = '1234';

        $posts = $user->getPosts();

        $post = $posts->getFirst();
        $post->body = 'Changed Body';

        $post = new Model\Post([
            'title' => 'new post',
            'body' => 'Lorem Ipsum',
            'price' => 123.23,
        ]);

        $posts->add($post);

        $tags = Model\Tag::whereIn('id', [1, 2])->load();

        $post->getTags()->addModels($tags);

        $this->assertQueries([
            'SELECT `User`.* FROM `User` WHERE (`id` = 1) AND (`User`.`deletedAt` IS NULL) LIMIT 1',
            'SELECT `Address`.* FROM `Address` WHERE (`id` IN (1))',
            'SELECT `Post`.`class`, `Post`.* FROM `Post` WHERE (`userId` IN (1))',
            'SELECT `Tag`.* FROM `Tag` WHERE (`id` IN (1, 2))',
        ]);

        Model\User::save($user);

        $this->assertQueries([
            'SELECT `User`.* FROM `User` WHERE (`id` = 1) AND (`User`.`deletedAt` IS NULL) LIMIT 1',
            'SELECT `Address`.* FROM `Address` WHERE (`id` IN (1))',
            'SELECT `Post`.`class`, `Post`.* FROM `Post` WHERE (`userId` IN (1))',
            'SELECT `Tag`.* FROM `Tag` WHERE (`id` IN (1, 2))',
            'INSERT INTO `Post` (`id`, `title`, `body`, `price`, `tags`, `createdAt`, `updatedAt`, `publishedAt`, `userId`, `class`) VALUES (NULL, "new post", "Lorem Ipsum", "123.23", NULL, NULL, NULL, NULL, NULL, "Harp\\Harp\\Test\\Model\\Post")',
            'INSERT INTO `PostTag` (`id`, `postId`, `tagId`) VALUES (NULL, NULL, 1), (NULL, NULL, 2)',
            'UPDATE `User` SET `name` = "New Name", `isBlocked` = 1, `object` = "C:41:"Harp\Harp\Test\Integration\SaveableObject":22:{a:1:{i:0;s:5:"value";}}" WHERE (`id` = 1)',
            'UPDATE `Address` SET `zipCode` = "1234", `location` = "Somewhere else" WHERE (`id` = 1)',
            'UPDATE `Post` SET `body` = CASE `id` WHEN 1 THEN "Changed Body" ELSE `body` END, `userId` = CASE `id` WHEN 5 THEN 1 ELSE `userId` END WHERE (`id` IN (1, 5))',
            'UPDATE `PostTag` SET `postId` = CASE `id` WHEN 4 THEN "5" WHEN 5 THEN "5" ELSE `postId` END WHERE (`id` IN (4, 5))',
        ]);

        Container::clear();

        $user = Model\User::find(1);
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('value', $user->object->getVar());
        $this->assertEquals(true, $user->isBlocked);

        $address = $user->getAddress();
        $this->assertEquals('Somewhere else', $address->location);
        $this->assertEquals('1234', $address->zipCode);

        $posts = $user->getPosts();
        $post = $posts->getFirst();

        $this->assertEquals('Changed Body', $post->body);

        $newPost = Model\Post::where('title', 'new post')->loadFirst();

        $this->assertTrue($posts->has($newPost));

        $this->assertEquals([1, 2], $newPost->getTags()->get()->getIds());
    }
}
