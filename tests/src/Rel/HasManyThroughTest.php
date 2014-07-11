<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Test\TestModel\Post;
use Harp\Harp\Test\TestModel\Tag;
use Harp\Harp\Test\TestModel\PostTag;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Model\Models;
use Harp\Harp\Model\State;
use Harp\Harp\Rel\HasMany;
use Harp\Harp\Rel\HasManyThrough;
use Harp\Harp\Query\Select;
use Harp\Harp\Test\AbstractDbTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\HasManyThrough
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyThroughTest extends AbstractDbTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     * @covers ::getForeignKey
     * @covers ::getThroughRel
     * @covers ::getThroughRepo
     * @covers ::getThroughKey
     * @covers ::getThroughTable
     */
    public function testConstruct()
    {
        $rel = new HasManyThrough('tags', Post::getRepo()->getConfig(), Tag::getRepo(), 'postTags');

        $this->assertSame('tags', $rel->getName());
        $this->assertSame(Post::getRepo()->getConfig(), $rel->getConfig());
        $this->assertSame(Tag::getRepo(), $rel->getRepo());
        $this->assertSame('postId', $rel->getKey());
        $this->assertSame('tagId', $rel->getForeignKey());
        $this->assertSame('tagsKey', $rel->getThroughKey());
        $this->assertSame('postTags', $rel->getThroughTable());

        $this->assertSame(Post::getRepo()->getRel('postTags'), $rel->getThroughRel());
        $this->assertSame(PostTag::getRepo(), $rel->getThroughRepo());

        $rel = new HasManyThrough(
            'tags',
            Post::getRepo()->getConfig(),
            Tag::getRepo(),
            'postTags',
            ['key' => 'test', 'foreignKey' => 'test2']
        );
        $this->assertSame('test', $rel->getKey());
        $this->assertSame('test2', $rel->getForeignKey());
    }

    /**
     * @covers ::hasModels
     */
    public function testHasModels()
    {
        $rel = new HasManyThrough('tags', Post::getRepo()->getConfig(), Tag::getRepo(), 'postTags');

        $models = new Models([
            new Post(),
            new Post(),
        ]);

        $this->assertFalse($rel->hasModels($models));

        $models = new Models([
            new Post(['id' => null]),
            new Post(['id' => 2]),
        ]);

        $this->assertTrue($rel->hasModels($models));
    }

    /**
     * @covers ::loadModels
     */
    public function testModels()
    {
        $rel = new HasManyThrough('tags', Post::getRepo()->getConfig(), Tag::getRepo(), 'postTags');

        $models = new Models([
            new Post(['id' => 1]),
            new Post(['id' => 2]),
            new Post(['id' => 3]),
        ]);

        $tags = $rel->loadModels($models);

        $this->assertContainsOnlyInstancesOf('Harp\Harp\Test\TestModel\Tag', $tags);
        $this->assertCount(3, $tags);

        $this->assertEquals(1, $tags[0]->id);
        $this->assertEquals(2, $tags[1]->id);
        $this->assertEquals(2, $tags[1]->id);
    }

    public function dataAreLinked()
    {
        return [
            [new Post(['id' => 2]), new Tag(['id' => 12]), false],
            [new Post(['id' => 2]), new Tag(['id' => 5, 'tagsKey' => 2]), true],
        ];
    }

    /**
     * @covers ::areLinked
     * @dataProvider dataAreLinked
     */
    public function testAreLinked($model, $foreign, $expected)
    {
        $rel = new HasManyThrough('tags', Post::getRepo()->getConfig(), Tag::getRepo(), 'postTags');

        $this->assertEquals($expected, $rel->areLinked($model, $foreign));
    }

    /**
     * @covers ::delete
     * @covers ::insert
     */
    public function testUpdate()
    {
        $rel = new HasManyThrough('tags', Post::getRepo()->getConfig(), Tag::getRepo(), 'postTags');

        $model = new Post(['id' => 2]);
        $foreign1 = new Tag(['id' => 5]);
        $foreign2 = new Tag(['id' => 6]);
        $foreign3 = new Tag(['id' => 7]);

        $link1 = new PostTag(['tagId' => 5, 'postId' => 2], State::SAVED);
        $link2 = new PostTag(['tagId' => 6, 'postId' => 2], State::SAVED);

        $postTagsLink = new LinkMany($model, Post::getRepo()->getRel('postTags'), [$link1, $link2]);
        Post::getRepo()->addLink($postTagsLink);

        $link = new LinkMany($model, $rel, [$foreign1, $foreign2]);
        $link->remove($foreign1);
        $link->add($foreign3);

        $result = $rel->delete($link);

        $this->assertCount(1, $result);
        $this->assertSame($link1, $result->getFirst());
        $this->assertTrue($result->getFirst()->isDeleted());

        $this->assertFalse($postTagsLink->has($link1));
        $this->assertTrue($postTagsLink->has($link2));

        $result = $rel->insert($link);

        $this->assertCount(1, $result);
        $this->assertInstanceOf('Harp\Harp\Test\TestModel\PostTag', $result->getFirst());
        $this->assertTrue($result->getFirst()->isPending());
        $this->assertEquals(['postId' => 2, 'tagId' => 7, 'id' => null], $result->getFirst()->getProperties());
        $this->assertTrue($postTagsLink->has($result->getFirst()));
    }

    /**
     * @covers ::join
     */
    public function testJoin()
    {
        $rel = new HasManyThrough('tags', Post::getRepo()->getConfig(), Tag::getRepo(), 'postTags');

        $select = new Select(Post::getRepo());

        $rel->join($select, 'Post');

        $this->assertEquals(
            'SELECT `Post`.* FROM `Post` JOIN `PostTag` AS `postTags` ON `postTags`.`postId` = `Post`.`id` JOIN `Tag` AS `tags` ON `tags`.`id` = `postTags`.`tagId`',
            $select->humanize()
        );
    }

    /**
     * @covers ::join
     */
    public function testJoinSoftDelete()
    {
        $repo = Tag::getRepo();
        $repo->getConfig()->setSoftDelete(true);

        $rel = new HasManyThrough('tags', Post::getRepo()->getConfig(), $repo, 'postTags');

        $select = new Select(Post::getRepo());

        $rel->join($select, 'Address');

        $this->assertEquals(
            'SELECT `Post`.* FROM `Post` JOIN `PostTag` AS `postTags` ON `postTags`.`postId` = `Address`.`id` JOIN `Tag` AS `tags` ON `tags`.`id` = `postTags`.`tagId` AND `tags`.`deletedAt` IS NULL',
            $select->humanize()
        );
    }
}
