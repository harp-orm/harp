<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Rel\AbstractRel;
use Harp\Harp\Rel\AbstractRelMany;
use Harp\Harp\Model\Models;
use Harp\Harp\Model\State;
use Harp\Harp\Repo\LinkMany;
use Harp\Harp\Test\Repo\TestRepo;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Test\TestModel\User;
use Harp\Query\SQL\SQL;

/**
 * @coversDefaultClass Harp\Harp\Rel\AbstractRel
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class AbstractRelTest extends AbstractTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getName
     * @covers ::getConfig
     * @covers ::getRepo
     * @covers ::getInverseOf
     * @covers ::getInverseOfRel
     */
    public function testConstruct()
    {
        $config = City::getRepo()->getConfig();
        $repo = City::getRepo();
        $name = 'test name';

        $rel = $this->getMockForAbstractClass(
            'Harp\Harp\Rel\AbstractRel',
            [$name, $config, $repo, ['test' => 'test option', 'inverseOf' => 'country']]
        );

        $this->assertSame($name, $rel->getName());
        $this->assertSame($config, $rel->getConfig());
        $this->assertSame($repo, $rel->getRepo());
        $this->assertSame('country', $rel->getInverseOf());
        $this->assertSame(City::getRepo()->getRel('country'), $rel->getInverseOfRel());
        $this->assertSame('test option', $rel->test);
    }

    /**
     * @covers ::loadModelsIfAvailable
     */
    public function testLoadModelsIfAvailable()
    {
        $config = City::getRepo()->getConfig();
        $repo = City::getRepo();
        $name = 'test name';

        $rel = $this->getMockForAbstractClass(
            'Harp\Harp\Rel\AbstractRel',
            [$name, $config, $repo]
        );

        $models = new Models([new City(), new City()]);
        $expected = [new City(), new City(), new City()];

        $rel
            ->expects($this->exactly(2))
            ->method('hasModels')
            ->with($this->identicalTo($models))
            ->will($this->onConsecutiveCalls(false, true));

        $rel
            ->expects($this->once())
            ->method('loadModels')
            ->with($this->identicalTo($models))
            ->will($this->returnValue($expected));

        $result = $rel->loadModelsIfAvailable($models);

        $this->assertInstanceOf('Harp\Harp\Model\Models', $result);
        $this->assertEmpty($result);

        $result = $rel->loadModelsIfAvailable($models);

        $this->assertInstanceOf('Harp\Harp\Model\Models', $result);
        $this->assertSame($expected, $result->toArray());
    }

    /**
     * @covers ::linkModels
     */
    public function testLinkModels()
    {
        $models = [new City(), new City()];
        $foreign = [new City(), new City(), new City()];

        $map = [
            [$models[0], $foreign[0], true],
            [$models[0], $foreign[1], false],
            [$models[0], $foreign[2], false],
            [$models[1], $foreign[0], false],
            [$models[1], $foreign[1], true],
            [$models[1], $foreign[2], true],
        ];

        $rel = $this->getMockForAbstractClass(
            'Harp\Harp\Rel\AbstractRelMany',
            ['test name', City::getRepo()->getConfig(), City::getRepo()],
            '',
            true,
            true,
            true,
            ['newLinkFrom']
        );

        $rel
            ->expects($this->exactly(6))
            ->method('areLinked')
            ->will($this->returnValueMap($map));

        $links = [
            new LinkMany($models[0], $rel, [$foreign[0]]),
            new LinkMany($models[1], $rel, [$foreign[1], $foreign[2]]),
        ];


        $linkMap = [
            [$models[0], [$foreign[0]], $links[0]],
            [$models[1], [$foreign[1], $foreign[2]], $links[1]],
        ];

        $rel
            ->expects($this->exactly(2))
            ->method('newLinkFrom')
            ->will($this->returnValueMap($linkMap));

        $i = 0;

        $rel->linkModels(new Models($models), new Models($foreign), function($link) use ($models, $links, & $i) {
            $this->assertSame($models[$i], $link->getModel());
            $this->assertSame($links[$i], $link);
            $i++;
        });
    }

    /**
     * @covers ::getSoftDeleteConditions
     */
    public function testGetSoftDeleteConditions()
    {
        $rel = $this->getMockForAbstractClass(
            'Harp\Harp\Rel\AbstractRel',
            ['test', City::getRepo()->getConfig(), City::getRepo()]
        );

        $this->assertEquals([], $rel->getSoftDeleteConditions());

        $rel = $this->getMockForAbstractClass(
            'Harp\Harp\Rel\AbstractRel',
            ['test', User::getRepo()->getConfig(), User::getRepo()]
        );

        $this->assertEquals(['test.deletedAt' => new SQL('IS NULL')], $rel->getSoftDeleteConditions());
    }

    /**
     * @covers ::findAllWhereIn
     */
    public function testFindAllWhereIn()
    {
        $rel = $this->getMockForAbstractClass(
            'Harp\Harp\Rel\AbstractRel',
            ['test', User::getRepo()->getConfig(), User::getRepo()]
        );

        $find = $rel->findAllWhereIn('name', [10, 13], State::DELETED);

        $expected = 'SELECT `User`.* FROM `User` WHERE (`name` IN (10, 13)) AND (`User`.`deletedAt` IS NOT NULL)';

        $this->assertEquals($expected, $find->applyFlags()->humanize());
    }
}
