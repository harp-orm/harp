<?php

namespace Harp\Harp\Test\Rel;

use Harp\Harp\Config;
use Harp\Harp\Rel\HasOne;
use Harp\Harp\Rel\BelongsTo;
use Harp\Harp\Rel\BelongsToPolymorphic;
use Harp\Harp\Rel\HasMany;
use Harp\Harp\Rel\HasManyExclusive;
use Harp\Harp\Rel\HasManyAs;
use Harp\Harp\Rel\HasManyThrough;
use Harp\Harp\Test\AbstractTestCase;

/**
 * @coversDefaultClass Harp\Harp\Rel\RelConfigTrait
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class RelConfigTraitTest extends AbstractTestCase
{
    /**
     * @covers ::getRel
     * @covers ::getRels
     * @covers ::getRelOrError
     */
    public function testRels()
    {
        $config = new Config('Harp\Harp\Test\TestModel\City');

        $rels = $config->getRels();

        $this->assertSame($rels['country'], $config->getRel('country'));
        $this->assertSame($rels['country'], $config->getRelOrError('country'));
        $this->assertNull($config->getRel('other'));

        $this->setExpectedException('InvalidArgumentException');

        $config->getRelOrError('other');
    }

    /**
     * @covers ::belongsTo
     */
    public function testBelongsTo()
    {
        $config = new Config('Harp\Harp\Test\TestModel\City');
        $config->belongsTo('country', 'Harp\Harp\Test\TestModel\Country', ['inverseOf' => 'cities']);

        $this->assertEquals(
            new BelongsTo('country', $config, 'Harp\Harp\Test\TestModel\Country', ['inverseOf' => 'cities']),
            $config->getRel('country')
        );
    }

    /**
     * @covers ::belongsToPolymorphic
     */
    public function testBelongsToPolymorphic()
    {
        $config = new Config('Harp\Harp\Test\TestModel\City');
        $config->belongsToPolymorphic('country', 'Harp\Harp\Test\TestModel\Country', ['inverseOf' => 'cities']);

        $this->assertEquals(
            new BelongsToPolymorphic('country', $config, 'Harp\Harp\Test\TestModel\Country', ['inverseOf' => 'cities']),
            $config->getRel('country')
        );
    }

    /**
     * @covers ::hasOne
     */
    public function testHasOne()
    {
        $config = new Config('Harp\Harp\Test\TestModel\City');
        $config->hasOne('country', 'Harp\Harp\Test\TestModel\Country', ['inverseOf' => 'cities']);

        $this->assertEquals(
            new HasOne('country', $config, 'Harp\Harp\Test\TestModel\Country', ['inverseOf' => 'cities']),
            $config->getRel('country')
        );
    }

    /**
     * @covers ::hasMany
     */
    public function testHasMany()
    {
        $config = new Config('Harp\Harp\Test\TestModel\Country');
        $config->hasMany('cities', 'Harp\Harp\Test\TestModel\City', ['inverseOf' => 'country']);

        $this->assertEquals(
            new HasMany('cities', $config, 'Harp\Harp\Test\TestModel\City', ['inverseOf' => 'country']),
            $config->getRel('cities')
        );
    }

    /**
     * @covers ::hasManyExclusive
     */
    public function testHasManyExclusive()
    {
        $config = new Config('Harp\Harp\Test\TestModel\Country');
        $config->hasManyExclusive('cities', 'Harp\Harp\Test\TestModel\City', ['inverseOf' => 'country']);

        $this->assertEquals(
            new HasManyExclusive('cities', $config, 'Harp\Harp\Test\TestModel\City', ['inverseOf' => 'country']),
            $config->getRel('cities')
        );
    }

    /**
     * @covers ::hasManyAs
     */
    public function testHasManyAs()
    {
        $config = new Config('Harp\Harp\Test\TestModel\Country');
        $config->hasManyAs('cities', 'Harp\Harp\Test\TestModel\City', 'user', ['inverseOf' => 'country']);

        $this->assertEquals(
            new HasManyAs('cities', $config, 'Harp\Harp\Test\TestModel\City', 'user', ['inverseOf' => 'country']),
            $config->getRel('cities')
        );
    }


    /**
     * @covers ::hasManyThrough
     */
    public function testHasManyThrough()
    {
        $config = new Config('Harp\Harp\Test\TestModel\Country');
        $config->hasManyThrough('users', 'Harp\Harp\Test\TestModel\User', 'cities', ['foreignKey' => 'cityId']);

        $this->assertEquals(
            new HasManyThrough('users', $config, 'Harp\Harp\Test\TestModel\User', 'cities', ['foreignKey' => 'cityId']),
            $config->getRel('users')
        );
    }
}
