<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Repo\Links;
use Harp\Harp\Repo\LinkOne;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\Address;
use Harp\Harp\Test\TestModel\User;
use Harp\Harp\Test\TestModel\Post;

/**
 * @coversDefaultClass Harp\Harp\Repo\Links
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class LinksTest extends AbstractTestCase
{
    /**
     * @covers ::getModel
     * @covers ::__construct
     * @covers ::all
     */
    public function testConstruct()
    {
        $model = new User();
        $links = new Links($model);

        $this->assertSame($model, $links->getModel());
        $this->assertSame([], $links->all());
    }

    /**
     * @covers ::add
     */
    public function testAdd()
    {
        $links = new Links(new User());
        $linkOne = new LinkOne(new User(), User::getRepo()->getRel('address'), new Address());
        $linkMany = new LinkMany(new User(), User::getRepo()->getRel('posts'), []);

        $links
            ->add($linkOne)
            ->add($linkMany);

        $expected = [
            'address' => $linkOne,
            'posts' => $linkMany,
        ];

        $this->assertSame($expected, $links->all());
    }

    /**
     * @covers ::getModels
     */
    public function testGetModels()
    {
        $model1 = new Address();
        $model2 = new Post();
        $model3 = new Post();

        $links = new Links(new User());
        $linkOne = new LinkOne(new User(), User::getRepo()->getRel('address'), new Address());
        $linkMany = new LinkMany(new User(), User::getRepo()->getRel('posts'), []);

        $linkOne->set($model1);

        $linkMany
            ->add($model2)
            ->add($model3);

        $links
            ->add($linkOne)
            ->add($linkMany);

        $result = $links->getModels();

        $this->assertInstanceOf('Harp\Harp\Model\Models', $result);
        $this->assertTrue($result->has($model1));
        $this->assertTrue($result->has($model2));
        $this->assertTrue($result->has($model3));
    }

    /**
     * @covers ::isEmpty
     */
    public function testIsEmpty()
    {
        $links = new Links(new User());
        $linkOne = new LinkOne(new User(), User::getRepo()->getRel('address'), new Address());

        $this->assertTrue($links->isEmpty());

        $links->add($linkOne);

        $this->assertFalse($links->isEmpty());
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $links = new Links(new User());
        $linkOne = new LinkOne(new User(), User::getRepo()->getRel('address'), new Address());

        $this->assertFalse($links->has('address'));

        $links->add($linkOne);

        $this->assertTrue($links->has('address'));
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $links = new Links(new User());
        $linkOne = new LinkOne(new User(), User::getRepo()->getRel('address'), new Address());

        $this->assertNull($links->get('address'));

        $links->add($linkOne);

        $this->assertEquals($linkOne, $links->get('address'));
    }
}
