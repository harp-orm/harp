<?php

namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Luna\Mapper\Repo;
use CL\Luna\ModelQuery\Union;
use CL\Luna\MassAssign\Data;

class TestTest extends AbstractTestCase {

    public function testMassAssign()
    {
        Log::setEnabled(TRUE);

        $user3 = UserStore::get()->find(3);

        $data = new Data([
            'posts' => [
                [
                    'title' => 'my title',
                    'body' => 'my body',
                ],
                [
                    'title' => 'my title 2',
                    'body' => 'my body 2',
                ]
            ],
            'address' => [
                '_id' => 1,
                'zipCode' => 2222,
            ],
            'name' => 'new name!!',
        ], [
            'posts' => ['title', 'body'],
            'address' => ['_id', 'zipCode'],
            'name',
        ]);

        $data->assignTo($user3);

        Repo::get()->persist($user3);

        $this->assertEquals(
            [
                'SELECT User.* FROM User WHERE (User.id = 3) AND (User.deletedAt IS NULL) LIMIT 1',
                'SELECT Post.polymorphicClass, Post.* FROM Post WHERE (userId IN (3))',
                'SELECT Address.* FROM Address WHERE (Address.id = 1) LIMIT 1',
                'INSERT INTO Post (id, title, body, price, tags, createdAt, updatedAt, publishedAt, userId, polymorphicClass) VALUES (NULL, "my title", "my body", NULL, NULL, NULL, NULL, NULL, NULL, "CL\Luna\Test\Post"), (NULL, "my title 2", "my body 2", NULL, NULL, NULL, NULL, NULL, NULL, "CL\Luna\Test\Post")',
                'UPDATE User SET name = "new name!!", addressId = 1 WHERE (User.id = 3) AND (User.deletedAt IS NULL)',
                'UPDATE Post SET userId = CASE id WHEN 5 THEN 3 WHEN 6 THEN 3 ELSE userId END WHERE (id IN (5, 6))',
                'UPDATE Post SET userId = NULL WHERE (Post.id = 4)',
                'UPDATE Address SET zipCode = 2222 WHERE (Address.id = 1)',
            ],
            Log::all()
        );
    }

    public function testPolymorphic()
    {
        $post = PostStore::get()->find(4);

        $this->assertInstanceOf('CL\Luna\Test\BlogPost', $post);
        $this->assertTrue($post->isPublished);

        $this->assertNotSame(PostStore::get(), BlogPostStore::get());
    }

    public function testHasManyThrough()
    {
        Log::setEnabled(TRUE);

        $post = PostStore::get()->find(1);

        $tag1 = TagStore::get()->find(1);
        $tag2 = TagStore::get()->find(2);

        $tags = $post->getTags();

        $this->assertCount(2, $tags);
        $this->assertTrue($tags->has($tag1));

        $this->assertEquals(
            [
                'SELECT Post.polymorphicClass, Post.* FROM Post WHERE (Post.id = 1) LIMIT 1',
                'SELECT Tag.* FROM Tag WHERE (Tag.id = 1) LIMIT 1',
                'SELECT Tag.* FROM Tag WHERE (Tag.id = 2) LIMIT 1',
                'SELECT Tag.*, postTags.postId AS tagsKey FROM Tag JOIN PostTag AS postTags ON postTags.tagId = Tag.id WHERE (postTags.PostId IN (1))',
           ],
            Log::all()
        );
    }

    public function testLoadIds()
    {
        Log::setEnabled(true);

        $ids = PostStore::get()->findAll()->whereKeys([1,2,3])->loadIds();

        $expected = array(1, 2, 3);

        $this->assertEquals($expected, $ids);

        $this->assertEquals(
            [
                'SELECT Post.* FROM Post WHERE (Post.id IN (1, 2, 3))',
            ],
            Log::all()
        );
    }

    public function testLoadWith()
    {
        Log::setEnabled(TRUE);

        $posts = PostStore::get()->findAll()->loadWith(['user' => ['address', 'location']]);

        $user1 = $posts[0]->getUser();
        $user2 = $posts[1]->getUser();

        $address1 = $posts[0]->getUser()->getAddress();
        $address2 = $posts[1]->getUser()->getAddress();

        $location1 = $posts[0]->getUser()->getLocation();
        $location2 = $posts[1]->getUser()->getLocation();

        $this->assertEquals(
            [
                'SELECT Post.polymorphicClass, Post.* FROM Post',
                'SELECT User.* FROM User WHERE (id IN (1, 4, 5, 3)) AND (User.deletedAt IS NULL)',
                'SELECT Address.* FROM Address WHERE (id IN (1))',
                'SELECT City.* FROM City WHERE (id IN (1))',
                'SELECT Country.* FROM Country WHERE (id IN (1, 2))',
            ],
            Log::all()
        );

        $this->assertSame($address1, $address2);
        $this->assertEquals(
            [
                'id' => 1,
                'name' => "User 1",
                'password' => null,
                'addressId' => 1,
                'parentId' => null,
                'isBlocked' => false,
                'deletedAt' => null,
                'locationId' => 1,
                'locationClass' => 'CL\Luna\Test\City',
            ],
            $user1->getFieldValues()
        );

        $this->assertEquals(
            [
                'id' => 4,
                'name' => "User 4",
                'password' => null,
                'addressId' => 1,
                'parentId' => null,
                'isBlocked' => false,
                'deletedAt' => null,
                'locationId' => 2,
                'locationClass' => 'CL\Luna\Test\Country',
            ],
            $user2->getFieldValues()
        );
    }
}
