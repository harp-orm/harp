<?php namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Luna\Mapper\Repo;
use CL\Luna\ModelQuery\Union;
use CL\Luna\MassAssign\Data;

class TestTest extends AbstractTestCase {

    public function testMassAssign()
    {
        Log::setEnabled(TRUE);

        $user3 = User::find(3);

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
                'SELECT User.* FROM User WHERE (User.deletedAt IS NULL) AND (id = 3) LIMIT 1',
                'SELECT Post.polymorphicClass, Post.* FROM Post WHERE (userId IN (3))',
                'SELECT Address.* FROM Address WHERE (id = 1) LIMIT 1',
                'INSERT INTO Post (id, title, body, price, tags, createdAt, updatedAt, publishedAt, userId, polymorphicClass) VALUES (NULL, "my title", "my body", NULL, NULL, NULL, NULL, NULL, 3, "CL\Luna\Test\Post"), (NULL, "my title 2", "my body 2", NULL, NULL, NULL, NULL, NULL, 3, "CL\Luna\Test\Post")',
                'UPDATE User SET name = "new name!!", addressId = 1 WHERE (User.deletedAt IS NULL) AND (id = 3)',
                'UPDATE Post SET userId = NULL WHERE (id = 4)',
                'UPDATE Address SET zipCode = 2222 WHERE (id = 1)',
            ],
            Log::all()
        );
    }

    public function testPolymorphic()
    {
        $post = Post::find(4);

        $this->assertInstanceOf('CL\Luna\Test\BlogPost', $post);
        $this->assertTrue($post->isPublished);

        $this->assertNotSame(Post::getSchema(), BlogPost::getSchema());
    }

    public function testHasManyThrough()
    {
        Log::setEnabled(TRUE);

        $post = Post::find(1);

        $tag1 = Tag::find(1);
        $tag2 = Tag::find(2);

        $tags = $post->getTags();

        $this->assertCount(2, $tags);
        $this->assertTrue($tags->has($tag1));

        $this->assertEquals(
            [
                'SELECT Post.polymorphicClass, Post.* FROM Post WHERE (id = 1) LIMIT 1',
                'SELECT Tag.* FROM Tag WHERE (id = 1) LIMIT 1',
                'SELECT Tag.* FROM Tag WHERE (id = 2) LIMIT 1',
                'SELECT Tag.*, postTags.postId AS tagsKey FROM Tag JOIN PostTag AS postTags ON postTags.tagId = Tag.id WHERE (postTags.PostId IN (1))',
           ],
            Log::all()
        );
    }

    public function testEagerLoad()
    {
        Log::setEnabled(TRUE);

        $posts = Post::findAll()->eagerLoad(['user' => ['address', 'location']]);

        $user1 = $posts[0]->getUser();
        $user2 = $posts[1]->getUser();

        $address1 = $posts[0]->getUser()->getAddress();
        $address2 = $posts[1]->getUser()->getAddress();

        $location1 = $posts[0]->getUser()->getLocation();
        $location2 = $posts[1]->getUser()->getLocation();

        $this->assertEquals(
            [
                'SELECT Post.polymorphicClass, Post.* FROM Post',
                'SELECT User.* FROM User WHERE (User.deletedAt IS NULL) AND (id IN (1, 4, 5, 3))',
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
