<?php namespace CL\Luna\Test;

use CL\Luna\Util\Log;
use CL\Atlas\Query\InsertQuery;
use CL\Luna\Mapper\Repo;
use CL\Luna\MassAssign\UnsafeData;

class TestTest extends AbstractTestCase {

    public function testMassAssign()
    {
        Log::setEnabled(TRUE);

        $user3 = User::get(3);

        $data = new UnsafeData([
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
        ]);

        $data->assign($user3);

        Repo::get()->persist($user3);

        $this->assertEquals(
            [
                'SELECT user.* FROM user WHERE (user.deletedAt IS NULL) AND (id = 3) LIMIT 1',
                'SELECT post.* FROM post WHERE (userId IN (3))',
                'SELECT address.* FROM address WHERE (id = 1) LIMIT 1',
                'INSERT INTO post (id, title, body, price, tags, createdAt, updatedAt, publishedAt, userId) VALUES (NULL, "my title", "my body", NULL, NULL, NULL, NULL, NULL, 3), (NULL, "my title 2", "my body 2", NULL, NULL, NULL, NULL, NULL, 3)',
                'UPDATE user SET name = "new name!!", addressId = 1 WHERE (user.deletedAt IS NULL) AND (id = 3)',
                'UPDATE address SET zipCode = 2222 WHERE (id = 1)',
            ],
            Log::all()
        );
    }

    public function testLoadWith()
    {
        Log::setEnabled(TRUE);

        $posts = Post::all()->loadWith(['user' => 'address']);

        $user1 = $posts[0]->getUser();
        $user2 = $posts[1]->getUser();

        $address1 = $posts[0]->getUser()->getAddress();
        $address2 = $posts[1]->getUser()->getAddress();

        $this->assertEquals(
            [
                'SELECT post.* FROM post',
                'SELECT user.* FROM user WHERE (user.deletedAt IS NULL) AND (id IN (1, 4, 5))',
                'SELECT address.* FROM address WHERE (id IN (1))',
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
                'isBlocked' => null,
                'deletedAt' => null,
            ],
            $user2->getFieldValues()
        );
    }

}
